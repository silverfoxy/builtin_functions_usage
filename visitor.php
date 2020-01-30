<?php
declare(strict_types=1);

require 'vendor/autoload.php';

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class Visitor extends NodeVisitorAbstract {
    public $file_name;
    public $script_dir;
    public array $builtin_functions;
    public int $dynamic_calls = 0;

    public function __construct($file_name, $builtin_functions) {
        $this->file_name = $file_name;
        $this->script_dir = pathinfo($file_name)['dirname'];
        $this->builtin_functions = $builtin_functions;
    }

    public function enterNode(Node $node) {
        $method_call = new Node\Expr\MethodCall(new Node\Expr\Exit_(), null);
        $func_call = new Node\Expr\FuncCall(null);
        $static_call = new Node\Expr\StaticCall(null, null);
        $node_name = null;
        switch ($node->getType()) {
            case $method_call->getType():
            case $func_call->getType():
                if ($node->name === 'error_log') {
                    // Skip error logs
                    return $node;
                }
                if (is_string($node->name)) {
                    $node_name = $node->name;
                }
                else if ($node->name instanceof Node\Name) {
                    $node_name = implode('', $node->name->parts);
                }
                else if ($node->name instanceof Node\Identifier) {
                    $node_name = $node->name->name;
                }
                else if($node->name instanceof Node\Expr) {
                    // Dynamic call, skip
                    $this->dynamic_calls++;
                    return $node;
                }
                break;
            case $static_call->getType():
                if (is_string($node->class) && is_string($node->name)) {
                    $node_name = $node->class.'::'.$node->name;
                }
                else {
                    $class_name = $node->class;
                    $func_name = $node->name;
                    if ($node->class instanceof Node\Name) {
                        $class_name = implode('', $node->class->parts);
                    }
                    else if ($node->class instanceof Node\Expr) {
                        // Dynamic call, skip
                        $this->dynamic_calls++;
                        return $node;
                    }
                    if ($node->name instanceof Node\Name) {
                        $func_name = implode('', $node->name->parts);
                    }
                    else if ($node->name instanceof Node\Expr) {
                        // Dynamic call, skip
                        $this->dynamic_calls++;
                        return $node;
                    }
                    $node_name = $class_name.'::'.$func_name;
                }
                break;
        }
        if (null !== $node_name) {
            try {
                $node_name = strval($node_name);
            }
            catch(Error $ex) {
                var_dump($node_name);
            }
            if (array_key_exists($node_name, $this->builtin_functions)) {
                $this->builtin_functions[$node_name]++;
            }
        }
    }
}