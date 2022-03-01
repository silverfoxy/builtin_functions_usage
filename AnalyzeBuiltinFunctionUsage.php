<?php

declare(strict_types=1);

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

require_once 'vendor/autoload.php';
require_once 'visitor.php';
require_once 'sensitivefunctionanalysis.php';

class AnalyzeBuiltinFunctionUsage {
    protected array $builtin_functions;
    protected string $target_dir;

    public function __construct($target_dir) {
        $this->builtin_functions = file('php_builtinfunctions.list', FILE_IGNORE_NEW_LINES);
        $this->target_dir = $target_dir;
    }

    public function extract_usage() {
        $this->builtin_functions = array_fill_keys($this->builtin_functions, 0);
         // For each file
        $files = AnalyzeBuiltinFunctionUsage::getDirContents($this->target_dir);
        foreach ($files as $key => $file_name) {
            if (array_key_exists('extension', pathinfo($file_name)) && ((pathinfo($file_name)['extension'] == 'php') || (pathinfo($file_name)['extension'] == 'inc') || (pathinfo($file_name)['extension'] == 'module'))) {
                $code = file_get_contents($file_name);
                $parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP5);
                try {
                    $ast = $parser->parse($code);
                    // var_dump($ast);
                    // return;
                } catch (PhpParser\Error $error) {
                    if (substr($code, 0, 4) === '<?hh') {
                        echo "Skipping HHVM file {$file_name}" . PHP_EOL;
                    }
                    else {
                        echo "Parse error at {$file_name}: {$error->getMessage()}" . PHP_EOL;
                    }
                }

                $traverser = new PhpParser\NodeTraverser;
                // Parse once to extract variable, function and class definitions
                $visitor = new BuiltinFunctionVisitor($file_name, $this->builtin_functions);
                $traverser->addVisitor($visitor);
                $traverser->traverse($ast);
                $this->builtin_functions = $visitor->builtin_functions;
            }
        }
        arsort($this->builtin_functions);
        $result = array();
        $total = 0;
        foreach ($this->builtin_functions as $key => $value) {
            if ($value > 0) {
                $result[$key] = $value;
                $total++;
            }
        }
        echo 'Total: '.$total.PHP_EOL;
        return $result;
    }

    public static function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);
        foreach($files as $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if (!$path) {
                // Directory not found or permission denied
                continue;
            }
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                AnalyzeBuiltinFunctionUsage::getDirContents($path, $results);
                //$results[] = $path;
            }
        }

        return $results;
    }
}