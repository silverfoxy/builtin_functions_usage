<?php

declare(strict_types=1);

require 'vendor/autoload.php';

class SensitiveFunctionAnalysis {

    public array $command_execution = ['exec', 'passthru', 'system', 'shell_exec', 'popen', 'proc_open', 'pcntl_exec', 'backticks', 'expect_popen', 'shell_exec', 'w32api_invoke_function', 'w32api_register_function'];
    public array $php_code_execution = ['eval', 'assert', 'create_function', 'preg_replace', 'include', 'include_once', 'require', 'require_once', 'ReflectionFunction', 'mb_ereg_replace', 'mb_eregi_replace', 'preg_filter', 'php_check_syntax', 'set_include_path', 'virtual',
                                        'yaml_parse', 'unserialize'];
    public array $callbacks = ['ob_start', 'array_diff_uassoc', 'array_diff_ukey', 'array_filter', 'array_intersec_uassoc', 'array_intersect_ukey', 'array_map',
     'array_reduce', 'array_udiff_assoc', 'array_udiff_uassoc', 'array_udiff', 'array_uintersect_assoc', 'array_uintersect_uassoc', 'array_intersect_uassoc', 'array_intersect_ukey', 'array_uintersect', 'array_walk_recursive', 
     'array_walk', 'assert_options', 'uasort', 'uksort', 'usort', 'preg_replace_callback', 'spl_autoload_register', 'iterator_apply', 'call_user_func', 'call_user_func_array', 
     'register_shutdown_function', 'register_tick_function', 'set_error_handler', 'set_exception_handler', 'session_set_save_handler', 'sqlite_create_aggregate', 'sqlite_create_function', 'forward_static_call', 'forward_static_call_arraycall_user_func_array',
     'yaml_parse', 'yaml_parse_file', 'yaml_parse_url'];
    public array $information_disclosure = ['phpinfo', 'posix_mkfifo', 'posix_getlogin', 'posix_ttyname', 'getenv', 'get_current_user', 'proc_get_status', 'get_cfg_var', 'disk_free_space', 
     'disk_total_space', 'diskfreespace', 'getcwd', 'getlastmo', 'getmygid', 'getmyinode', 'getmypid', 'getmyuid'];
    public array $other = ['extract', 'parse_str', 'putenv', 'ini_set', 'mail', 'mb_send_mail', 'header', 'proc_nice', 'proc_terminate', 'proc_close', 'pfsockopen', 'fsockopen', 
     'apache_child_terminate', 'posix_kill', 'posix_setpgid', 'posix_setsid', 'posix_setuid', 'dotnet_load'];
    public array $filesystem = ['fopen', 'tmpfile', 'bzopen', 'gzopen', 'chgrp', 'chmod', 'chown', 'copy', 'file_put_contents', 'lchgrp', 'lchown', 'link', 'mkdir', 'move_uploaded_file', 
     'rename', 'rmdir', 'symlink', 'tempnam', 'touch', 'unlink', 'imagepng', 'imagewbmp', 'image2wbmp', 'imagejpeg', 'imagexbm', 'imagegif', 'imagegd', 'imagegd2', 'iptcembed', 
     'ftp_get', 'gtp_nb_get', 'file_exists', 'eio_busy', 'eio_chmod', 'eio_chown', 'eio_close', 'eio_custom', 'eio_dup2', 'eio_fallocate', 'eio_fchmod', 'eio_fchown', 'eio_fdatasync', 'eio_fstat', 'eio_fstatvfs',
     'bzread', 'bzflush', 'dio_read', 'eio_readdir', 'fdf_open', 'file', 'file_get_contents', 'finfo_file', 'fflush', 'fgetc', 'fgetcsv', 'fgets', 'fgetss', 'fread', 'fpassthru', 'fscanf', 'ftok',
     'get_meta_tags', 'glob', 'gzfile', 'gzgetc', 'gzgets', 'gzgetss', 'gzread', 'gzpassthru', 'highlight_file', 'imagecreatefrompng', 'imagecreatefromjpg', 'imagecreatefromgif', 'imagecreatefromgd2', 
     'imagecreatefromgd2part', 'imagecreatefromgd', 'opendir', 'parse_ini_file', 'php_strip_whitespace', 'readfile', 'readgzfile', 'readlink', 'stat', 'scandir', 'show_source', 'simplexml_load_file', 'stream_get_contents', 
     'stream_get_line', 'xdiff_file_bdiff', 'xdiff_file_bpatch', 'xdiff_file_diff_binary', 'xdiff_file_diff', 'xdiff_file_merge3', 'xdiff_file_patch_binary', 'xdiff_file_patch', 'xdiff_file_rabdiff', 'yaml_parse_file', 'zip_open',
     'bzwrite', 'dio_write', 'eio_chmod', 'eio_chown', 'eio_mkdir', 'eio_mknod', 'eio_rmdir', 'eio_write', 'eio_unlink', 'event_buffer_write', 'file_put_contents', 'fputcsv', 'fputs', 'fprintf', 'ftruncate', 'fwrite', 
     'gzwrite', 'gzputs', 'loadXML', 'move_uploaded_file', 'posix_mknod', 'recode_file', 'shmop_write', 'vfprintf', 'xdiff_file_bdiff', 'xdiff_file_bpatch', 'xdiff_file_diff_binary', 'xdiff_file_diff', 'xdiff_file_merge3', 
     'xdiff_file_patch_binary', 'xdiff_file_patch', 'xdiff_file_rabdiff', 'yaml_emit_file'];
    public array $database = ['dba_open', 'dba_popen', 'dba_insert', 'dba_fetch', 'dba_delete', 'dbx_query', 'odbc_do', 'odbc_exec', 'odbc_execute', 'db2_exec', 'db2_execute', 'fbsql_db_query', 'fbsql_query', 'ibase_query', 
                              'ibase_execute', 'ifx_query', 'ifx_do', 'ingres_query', 'ingres_execute', 'ingres_unbuffered_query', 'msql_db_query', 'msql_query', 'msql', 'mssql_query', 'mssql_execute', 'mysql_db_query', 
                              'mysql_query', 'mysql_unbuffered_query', 'mysqli_stmt_execute', 'mysqli_query', 'mysqli_real_query', 'mysqli_master_query', 'oci_execute', 'ociexecute', 'ovrimos_exec', 'ovrimos_execute', 'ora_do', 
                              'ora_exec', 'pg_query', 'pg_send_query', 'pg_send_query_params', 'pg_send_prepare', 'pg_prepare', 'sqlite_open', 'sqlite_popen', 'sqlite_array_query', 'arrayQuery', 'singleQuery', 'sqlite_query', 
                              'sqlite_exec', 'sqlite_single_query', 'sqlite_unbuffered_query', 'sybase_query', 'sybase_unbuffered_query'];
    public array $xss = ['echo', 'print', 'print_r', 'exit', 'die', 'printf', 'vprintf', 'trigger_error', 'user_error', 'odbc_result_all', 'ovrimos_result_all', 'ifx_htmltbl_result'];
    
    public array $diff_results = array();
    public array $command_execution_calls = [0, 0];
    public array $php_code_execution_calls = [0, 0];
    public array $callback_calls = [0, 0];
    public array $information_disclosure_calls = [0, 0];
    public array $other_calls = [0, 0];
    public array $filesystem_calls = [0, 0];
    public array $database_calls = [0, 0];
    public array $xss_calls = [0, 0];

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
            if (in_array($key, $this->database)) {
                $this->database_calls[0] += $original_builtin_functions[$key];
                $this->database_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
            if (in_array($key, $this->xss)) {
                $this->xss_calls[0] += $original_builtin_functions[$key];
                $this->xss_calls[1] += @$debloated_builtin_functions[$key] ? $debloated_builtin_functions[$key] : 0;
            }
        }
        arsort($this->diff_results);
    }
}