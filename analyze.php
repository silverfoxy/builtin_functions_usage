<?php

declare(strict_types=1);

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

require 'vendor/autoload.php';
require 'visitor.php';
require 'sensitivefunctionanalysis.php';

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
            if (array_key_exists('extension', pathinfo($file_name)) && ((pathinfo($file_name)['extension'] == 'php') || (pathinfo($file_name)['extension'] == 'inc'))) {
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
                $visitor = new Visitor($file_name, $this->builtin_functions);
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

$target_dir_1 = $argv[1];
$analyzer = new AnalyzeBuiltinFunctionUsage($target_dir_1);
$original_usage_results = $analyzer->extract_usage();

$target_dir_2 = $argv[2];
$analyzer = new AnalyzeBuiltinFunctionUsage($target_dir_2);
$debloated_usage_results = $analyzer->extract_usage();

$sensitivefunc_analyzer = new SensitiveFunctionAnalysis();
$sensitivefunc_analyzer->compare($original_usage_results, $debloated_usage_results);

// foreach ($sensitivefunc_analyzer->diff_results as $key => $value) {
//     echo $key . ':' . $value . PHP_EOL;
// }

echo 'Command execution:' . $sensitivefunc_analyzer->command_execution_calls[0] . '->' . $sensitivefunc_analyzer->command_execution_calls[1] . PHP_EOL;
echo 'PHP code execution:' . $sensitivefunc_analyzer->php_code_execution_calls[0] . '->' . $sensitivefunc_analyzer->php_code_execution_calls[1] . PHP_EOL;
echo 'Callbacks:' . $sensitivefunc_analyzer->callback_calls[0] . '->' . $sensitivefunc_analyzer->callback_calls[1] . PHP_EOL;
echo 'Information disclosure:' . $sensitivefunc_analyzer->information_disclosure_calls[0] . '->' . $sensitivefunc_analyzer->information_disclosure_calls[1] . PHP_EOL;
echo 'Other calls:' . $sensitivefunc_analyzer->other_calls[0] . '->' . $sensitivefunc_analyzer->other_calls[1] . PHP_EOL;
echo 'Filesystem calls:' . $sensitivefunc_analyzer->filesystem_calls[0] . '->' . $sensitivefunc_analyzer->filesystem_calls[1] . PHP_EOL;
echo 'Database calls:' . $sensitivefunc_analyzer->database_calls[0] . '->' . $sensitivefunc_analyzer->database_calls[1] . PHP_EOL;
echo 'XSS calls:' . $sensitivefunc_analyzer->xss_calls[0] . '->' . $sensitivefunc_analyzer->xss_calls[1] . PHP_EOL;