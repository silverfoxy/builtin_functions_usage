<?php

declare(strict_types=1);

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

require_once 'vendor/autoload.php';
require_once 'visitor.php';
require_once 'sensitivefunctionanalysis.php';
require_once 'AnalyzeBuiltinFunctionUsage.php';

$dual_analysis = true;
if (sizeof($argv) < 3) {
    $dual_analysis = false;
}

$target_dir_1 = $argv[1];
$analyzer = new AnalyzeBuiltinFunctionUsage($target_dir_1);
$original_usage_results = $analyzer->extract_usage();

if ($dual_analysis === true) {
    $target_dir_2 = $argv[2];
    $analyzer = new AnalyzeBuiltinFunctionUsage($target_dir_2);
    $debloated_usage_results = $analyzer->extract_usage();

    $sensitivefunc_analyzer = new SensitiveFunctionAnalysis();
    $sensitivefunc_analyzer->compare($original_usage_results, $debloated_usage_results);

    echo 'Command execution:' . $sensitivefunc_analyzer->command_execution_calls[0] . '->' . $sensitivefunc_analyzer->command_execution_calls[1] . PHP_EOL;
    echo 'PHP code execution:' . $sensitivefunc_analyzer->php_code_execution_calls[0] . '->' . $sensitivefunc_analyzer->php_code_execution_calls[1] . PHP_EOL;
    echo 'Callbacks:' . $sensitivefunc_analyzer->callback_calls[0] . '->' . $sensitivefunc_analyzer->callback_calls[1] . PHP_EOL;
    echo 'Information disclosure:' . $sensitivefunc_analyzer->information_disclosure_calls[0] . '->' . $sensitivefunc_analyzer->information_disclosure_calls[1] . PHP_EOL;
    echo 'Other calls:' . $sensitivefunc_analyzer->other_calls[0] . '->' . $sensitivefunc_analyzer->other_calls[1] . PHP_EOL;
    echo 'Filesystem calls:' . $sensitivefunc_analyzer->filesystem_calls[0] . '->' . $sensitivefunc_analyzer->filesystem_calls[1] . PHP_EOL;
    echo 'Database calls:' . $sensitivefunc_analyzer->database_calls[0] . '->' . $sensitivefunc_analyzer->database_calls[1] . PHP_EOL;
    echo 'XSS calls:' . $sensitivefunc_analyzer->xss_calls[0] . '->' . $sensitivefunc_analyzer->xss_calls[1] . PHP_EOL;
}
else {
    $sensitivefunc_analyzer = new SensitiveFunctionAnalysis();
    $sensitivefunc_analyzer->analyze_single_app($original_usage_results);

    echo 'Command execution:' . $sensitivefunc_analyzer->command_execution_calls[0] . PHP_EOL;
    echo 'PHP code execution:' . $sensitivefunc_analyzer->php_code_execution_calls[0] . PHP_EOL;
    echo 'Callbacks:' . $sensitivefunc_analyzer->callback_calls[0] . PHP_EOL;
    echo 'Information disclosure:' . $sensitivefunc_analyzer->information_disclosure_calls[0] . PHP_EOL;
    echo 'Other calls:' . $sensitivefunc_analyzer->other_calls[0] . PHP_EOL;
    echo 'Filesystem calls:' . $sensitivefunc_analyzer->filesystem_calls[0] . PHP_EOL;
    echo 'Database calls:' . $sensitivefunc_analyzer->database_calls[0]  . PHP_EOL;
    echo 'XSS calls:' . $sensitivefunc_analyzer->xss_calls[0] . PHP_EOL;
}