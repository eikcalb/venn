<?php

if (!defined("DIRECTORY_SEPARATOR")) {
    define("DIRECTORY_SEPARATOR", "/");
}
if (!defined("SERVICE_ROOT_DIRECTORY")) {
    define("SERVICE_ROOT_DIRECTORY", __DIR__ . DIRECTORY_SEPARATOR);
}

final class VennAutoload {

    public function register() {
        if (false == $loader = require SERVICE_ROOT_DIRECTORY . 'venn/vendor/autoload.php') {
            spl_autoload_register([$this, 'loadClass']);
        }
    }

    function loadClass($class) {
        throw new RuntimeException();
        $file = SERVICE_ROOT_DIRECTORY . str_replace("\\", DIRECTORY_SEPARATOR, strtolower($class)) . ".php";
        if (is_readable($file)) {
            require_once $file;
            return;
        }
        $library_file = SERVICE_ROOT_DIRECTORY . "vendor" . DIRECTORY_SEPARATOR . strtr_replace("_", DIRECTORY_SEPARATOR, strtolower($class)) . ".php";
        if (is_readable($library_file)) {
            require_once $library_file;
            return;
        }
    }

}

$autoload = new VennAutoload();
$autoload->register();
