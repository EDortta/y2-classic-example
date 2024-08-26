<?php


$canCheckSyntax = function_exists('exec') && (strpos(ini_get('disable_functions'), 'exec') === false);
(function () {

    function loadFile($filename)
    {
        global $canCheckSyntax;

        // echo "Loading $filename canCheckSyntax=" . ($canCheckSyntax ? 'true' : 'false') . "\n";
        if ($canCheckSyntax) {
            exec('php -l ' . escapeshellarg($filename), $output, $return_var);
            if ($return_var === 0) {
                if (!is_file($filename . '.error')) {
                    try {
                        require_once ($filename);
                    } catch (Throwable $e) {
                        error_log('PHP error in file: ' . $filename);
                        touch($filename . '.error');
                    }
                }
            } else {
                error_log('PHP syntax error in file: ' . $filename);
                touch($filename . '.error');
            }
        } else {
            if (!is_file($filename . '.error')) {
                try {
                    require_once ($filename);
                } catch (Throwable $e) {
                    error_log('PHP error in file: ' . $filename);
                    touch($filename . '.error');
                }
            }
        }
    }

    function loadFromFolder($folder)
    {        
        foreach (glob($folder . '/api-*.php') as $filename) {
            loadFile($filename);
        }

        foreach (glob($folder . '/*', GLOB_ONLYDIR) as $subFolder) {
            loadFromFolder($subFolder);
        }
    }

    // echo '<pre>';
    $folders = ['api', 'pages', 'app', 'lib'];
    foreach ($folders as $folder) {
        loadFromFolder(__DIR__ . '/' . $folder);
    }
    // echo '</pre>';
})();
unset($canCheckSyntax);