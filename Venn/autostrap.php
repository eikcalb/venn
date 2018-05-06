<?php

if (!defined("DIRECTORY_SEPARATOR")) {
    define("DIRECTORY_SEPARATOR", "/");
}
if (!defined("SERVICE_ROOT_DIRECTORY")) {
    define("SERVICE_ROOT_DIRECTORY", __DIR__ . DIRECTORY_SEPARATOR);
}

spl_autoload_register(function($name) {
    $file = SERVICE_ROOT_DIRECTORY . str_replace("\\", DIRECTORY_SEPARATOR, strtolower($name)) . ".php";
    if (is_readable($file)) {
            require_once $file;
            return;
        }
        $library_file = SERVICE_ROOT_DIRECTORY . "library" . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, strtolower($name)) . ".php";
    if (is_readable($library_file)) {
        require_once $library_file;
        return;
        }
    }
);
