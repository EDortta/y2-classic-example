<?php

/**
 * This is an example of how to implement an API loader
 * for the YeAPF framework
 * 
 * Each file with the name starting with "api_" will be loaded
 */

foreach (glob(__DIR__ . "/api_*.php") as $filename) {
    exec("php -l " . escapeshellarg($filename), $output, $return_var);
    if ($return_var === 0) {
        require_once($filename);
    } else {
        error_log("PHP syntax error in file: " . $filename);
    }
}

