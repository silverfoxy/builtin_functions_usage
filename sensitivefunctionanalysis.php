<?php

declare(strict_types=1);

require 'vendor/autoload.php';

class SensitiveFunctionAnalysis {

    public array $command_execution = ['exec', 'passthru', 'system', 'shell_exec', 'popen', 'proc_open', 'pcntl_exec'];
    public array $php_code_execution = ['eval', 'assert', 'create_function', 'preg_replace', 'include', 'include_once', 'require', 'require_once', 'ReflectionFunction'];
    public array $callbacks = ['ob_start', 'array_diff_uassoc', 'array_diff_ukey', 'array_filter', 'array_intersec_uassoc', 'array_intersect_ukey', 'array_map',
     'array_reduce', 'array_udiff_assoc', 'array_udiff_uassoc', 'array_udiff', 'array_uintersect_assoc', 'array_uintersect_uassoc', 'array_uintersect', 'array_walk_recursive', 
     'array_walk', 'assert_options', 'uasort', 'uksort', 'usort', 'preg_replace_callback', 'spl_autoload_register', 'iterator_apply', 'call_user_func', 'call_user_func_array', 
     'register_shutdown_function', 'register_tick_function', 'set_error_handler', 'set_exception_handler', 'session_set_save_handler', 'sqlite_create_aggregate', 'sqlite_create_function'];
    public array $information_disclosure = ['phpinfo', 'posix_mkfifo', 'posix_getlogin', 'posix_ttyname', 'getenv', 'get_current_user', 'proc_get_status', 'get_cfg_var', 'disk_free_space', 
     'disk_total_space', 'diskfreespace', 'getcwd', 'getlastmo', 'getmygid', 'getmyinode', 'getmypid', 'getmyuid'];
    public array $other = ['extract', 'parse_str', 'putenv', 'ini_set', 'mail', 'header', 'proc_nice', 'proc_terminate', 'proc_close', 'pfsockopen', 'fsockopen', 
     'apache_child_terminate', 'posix_kill', 'posix_setpgid', 'posix_setsid', 'posix_setuid'];
    public array $filesystem = ['fopen', 'tmpfile', 'bzopen', 'gzopen', 'chgrp', 'chmod', 'chown', 'copy', 'file_put_contents', 'lchgrp', 'lchown', 'link', 'mkdir', 'move_uploaded_file', 
     'rename', 'rmdir', 'symlink', 'tempnam', 'touch', 'unlink', 'imagepng', 'imagewbmp', 'image2wbmp', 'imagejpeg', 'imagexbm', 'imagegif', 'imagegd', 'imagegd2', 'iptcembed', 
     'ftp_get', 'gtp_nb_get', 'file_exists'];
    
    public array $diff_results = array();
    public array $command_execution_calls = [0, 0];
    public array $php_code_execution_calls = [0, 0];
    public array $callback_calls = [0, 0];
    public array $information_disclosure_calls = [0, 0];
    public array $other_calls = [0, 0];
    public array $filesystem_calls = [0, 0];

    public function compare(array $original_builtin_functions, array $debloated_builtin_functions) {
        foreach ($original_builtin_functions as $key => $value) {
            // Measure the reduction in number of calls
            if (array_key_exists($key, $debloated_builtin_functions)) {
                $this->diff_results[$key] = $value - $debloated_builtin_functions[$key];
            }
            else {
                // All call sites removed
                $this->diff_results[$key] = $value;
            }
            // Count number of sensitive function calls and its reduction
            if (in_array($key, $this->command_execution)) {
                $this->command_execution_calls[0] += $original_builtin_functions[$key];
                $this->command_execution_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
            if (in_array($key, $this->php_code_execution)) {
                $this->php_code_execution_calls[0] += $original_builtin_functions[$key];
                $this->php_code_execution_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
            if (in_array($key, $this->callbacks)) {
                $this->callback_calls[0] += $original_builtin_functions[$key];
                $this->callback_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
            if (in_array($key, $this->information_disclosure)) {
                $this->information_disclosure_calls[0] += $original_builtin_functions[$key];
                $this->information_disclosure_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
            if (in_array($key, $this->other)) {
                $this->other_calls[0] += $original_builtin_functions[$key];
                $this->other_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
            if (in_array($key, $this->filesystem)) {
                $this->filesystem_calls[0] += $original_builtin_functions[$key];
                $this->filesystem_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
        }
        arsort($this->diff_results);
    }
}