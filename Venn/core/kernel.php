<?php

namespace core;

include '../autostrap.php'; //       REMOVE !!!!!
/*
 *  This is a class for running micro-services.
 * 
 *  It is the middleman between application components.
 *  Its good practice to use this class inside class definitions only.
 * 
 */

final class Kernel {

    private static $config, $instance, $db;
    private static $router;
    private static $appConfig = [];

    const PATH = "path";

    private function __construct() {
        if (($conf = file_get_contents(__DIR__ . "/config.json")) === false || file_exists(__DIR__ . "/config.json") === false) {
            throw new \Exception\FileNotFound;
        }
        $this->checkCanRun();
        Kernel::$config = json_decode($conf, true) or exit;
        Kernel::$instance = $this;
        $this->init();
    }

    protected function init() {
        header("Powered-By: " . Kernel::getName() . " " . Kernel::getVersion());
    }

    public static function bootstrap($bootstrapLocation = null) {
        Kernel::noop();
        if (empty($bootstrapLocation)) {
            $bootstrapLocation = "app.json";
        }
        Kernel::$appConfig = AssetManager::loadConfig($bootstrapLocation, "Could not find bootstrap config file. Usually stored in root folder as 'app.json'.");
        if (empty(Kernel::$appConfig)) {
            throw new \Exception\KernelException("Failed to bootstrap application. No bootstrap file was provided.");
        }
        return Kernel::loadComponent(Kernel::$appConfig['BOOTSTRAP'], null);
    }

//  Generates a token
    public static function genToken(array $head, array $body) {
//        $pkey = 
        return JWS::compose($head, $body, file_get_contents(Kernel::$config['internal']['app_access_path'] . "/key"), Kernel::$config['internal']['app_token']);
    }

    public static function getToken() {
        if (!empty(($authHeader = filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION')))) {
            list($type, $value) = explode(' ', $authHeader, 2);
            if (strcasecmp($type, "Bearer") === 0) {
                return $value;
            }
        }
    }

    public static function &getAppConfig() {
        if (!Kernel::isBootstrapped()) {
            throw new \Exception\KernelException("Application not yet initialized");
        }
        return Kernel::$appConfig;
    }

    public static function getRouter($refPath = null, $context = []) {
        if (!Kernel::isBootstrapped()) {
            throw new \Exception\KernelException("App must be botstrapped to perform this action!");
        }
        if (!empty($refPath) && is_string($refPath) && is_array($context)) {
            static::$router = new \request\Router($refPath, $context);
        }
        if (empty(static::$router)) {
            $routeconfig = static::$appConfig['route_config'];
            static::$router = new \request\Router(null, ['path' => $routeconfig['base_path'], 'domain' => $routeconfig['base_domain']]);
            unset($routeconfig);
        }
        return static::$router;
    }

    public static function isBootstrapped() {
        if (empty(Kernel::$appConfig) || !is_array(Kernel::$appConfig)) {
            return false;
        } else {
            return true;
        }
    }

    public static function isReady() {
        Kernel::noop();
        return Kernel::$config['internal']['ready'];
    }

    public static function isDevMode() {
        return Kernel::isReady();
    }

    public static function getVersion() {
        Kernel::noop();
        return Kernel::$config['internal']['app_version'];
    }

    public static function getName() {
        Kernel::noop();
        return Kernel::$config['internal']['app_name'];
    }

    public static function verifyToken($token) {
        return JWS::verify($token);
    }

    /**
     *   Instantiates a database connection without connecting to any database.
     *   The caller is responsible for connecting to a database.
     * 
     * @param int $dbi Database conection index in the @see Kernel database store
     * @return mixed Database instance
     */
    public static function db($dbi = 0) {
        Kernel::noop();
        if (empty(Kernel::$db[$dbi])) {
            $config = Kernel::$config['database'];
            Kernel::$db[$dbi] = mysqli_connect($config['address'], $config['username'], $config['password']) or exit;
        }
        return Kernel::$db[$dbi];
    }

//  Loads the microservice handler
    public static function loadService($service, $what) {
        $where = strtolower($service);
        $path = strtolower($what[Kernel::PATH]);
        unset($what[Kernel::PATH]);
        Kernel::noop();
        if (!array_key_exists($where, Kernel::$config['service'])) {
            throw new \Exception\ServiceNotFound("Service not registered", 404);
        }
        $loader = Kernel::$config["service"][$where];
        $loader['app_name'] = Kernel::$config['internal']['app_name'];
        $loader['app_version'] = Kernel::$config['internal']['app_version'];
        if (!empty($loader["loader"]) && is_callable('\\service\\' . $loader["loader"] . "::init")) {
            return call_user_func('\\service\\' . strtolower($loader["loader"]) . "::init", $loader["address"], $path, array_merge($loader, $what));
        } else {
            throw new \Exception\ServiceNotFound("Something went wrong", 501);
        }
    }

    public static function loadController($controller, $data) {
        if (stripos($controller, "\\app\\controller\\") === false) {
            $callable = "\\app\\controller\\$controller::start";
        } else {
            $callable = "$controller::start";
        }
        if (is_callable($callable)) {
            call_user_func($callable, $data);
        } else {
            throw new \Exception\ControllerException("Controller not found. Ensure controller name is properly specified!");
        }
    }

    public static function loadComponent($component, $data) {
        if (empty($component) || !is_string($component)) {
            throw new \InvalidArgumentException("Provided component must be a string");
        }
        if (stripos($component, "\\app\\component\\") === false) {
            $callable = "\\app\\component\\$component::start";
        } else {
            $callable = "$component::start";
        }
        if (is_callable($callable)) {
            $compinstance = call_user_func($callable, $data);
            if (!$compinstance instanceof \component\Component) {
                throw new \Exception\ComponentException("Provided Component '$component' must extend \component\Component class!");
            }
            return $compinstance;
        } else {
            throw new \Exception\ComponentException("Component '$component' not found. Ensure component name is properly specified!");
        }
    }

    private static function noop() {
        if (!isset(Kernel::$instance)) {
            new Kernel();
        }
    }

    protected static function checkCanRun() {
        // this will check configuration for required modules to run and verify if they are installed on the host machine
    }

}

//$a= Kernel::load("passport",['path'=>"/log",'method'=>'get']);
//var_dump($a);
