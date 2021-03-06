<?php

namespace Venn\core;

use Venn\request\Router;
use Venn\request\route\FileRouteLoader;

/*
 *  This is a class for running system processes.
 * 
 *  It is the middleman between application components.
 *  Its good practice to use this class inside class definitions only.
 * 
 */

final class Kernel {

    private static $config, $instance;
    private static $router, $db;
    private static $appConfig = [];
    private static $interceptor;
    private static $eventEmitter;

    const PATH = "path";

    private function __construct() {
        if (($conf = file_get_contents(__DIR__ . "/config.json")) === false || file_exists(__DIR__ . "/config.json") === false) {
            throw new \Venn\Exception\FileNotFound;
        }
        $this->checkCanRun();
        Kernel::$config = json_decode($conf, true) or exit;
        Kernel::$instance = &$this;
        $this->init();
    }

    protected function init() {
        header("Powered-By: " . Kernel::getName() . " " . Kernel::getVersion());
        Kernel::$eventEmitter = new event\EventEmitter();
        Kernel::$interceptor = new InterceptorManager();

        Kernel::on(event\Event::KERNEL_BOOTSTRAP, function() {
            Kernel::interceptRawRequest();
        });
        Kernel::on(event\Event::KERNEL_CLEANUP, function() {
            Kernel::interceptPostRequest();
        });
    }

    public static function bootstrap($bootstrapLocation = null) {
        Kernel::noop();
        if (empty($bootstrapLocation)) {
            $bootstrapLocation = "app.json";
        }
        Kernel::$appConfig = AssetManager::loadConfig($bootstrapLocation, "Could not find bootstrap config file. Usually stored in root folder as 'app.json'.");
        if (empty(Kernel::$appConfig)) {
            throw new \Venn\Exception\KernelException("Failed to bootstrap application. No bootstrap file was provided.");
        }
        // TODO: Run as async!---- !IMPORTANT!!
        Kernel::emit(event\Event::KERNEL_BOOTSTRAP);
        return Kernel::loadComponent(Kernel::$appConfig['BOOTSTRAP'], null);
    }

    public static function emit($event, event\Event $obj = null) {
        Kernel::$eventEmitter->emit($event, $obj);
    }

    public static function on($event, callable $listener) {
        Kernel::$eventEmitter->on($event, $listener);
    }

//  Generates a token
    public static function genToken(array $head, array $body) {
//        $pkey = 
        echo \Firebase\JWT\JWT::encode(['sub' => 555], file_get_contents(Kernel::$config['internal']['app_access_path'] . "/key")) . "<p>";
        return JWS::compose($head, $body, file_get_contents(Kernel::$config['internal']['app_access_path'] . "/key"), Kernel::$config['internal']['app_token']);
    }

    public static function getToken() {
        if (!empty(($authHeader = filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION')))) {
            list($type, $value) = explode(' ', $authHeader, 2);
            if (strcasecmp($type, "Bearer") === 0) {
                return $value;
            }
        }
        return false;
    }

    public static function &getAppConfig() {
        if (!Kernel::isBootstrapped()) {
            throw new \Venn\Exception\KernelException("Application not yet initialized");
        }
        return Kernel::$appConfig;
    }

    public static function getRouter(\Venn\request\route\RouteLoader $loader = null, $context = null) {
        if (!Kernel::isBootstrapped()) {
            throw new \Venn\Exception\KernelException("App must be botstrapped to perform this action!");
        }
        if (empty(static::$router)) {
            if (!empty($loader)) {
                static::$router = new Router($loader, $context);
            } else {
                $routeconfig = static::$appConfig['route_config'];
                static::$router = new Router(new FileRouteLoader(SERVICE_ROOT_DIRECTORY . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $routeconfig['route_dir']), DIRECTORY_SEPARATOR)), empty($context) ? ['path' => $routeconfig[Config::BASE_PATH], 'domain' => $routeconfig[Config::BASE_DOMAIN]] : $context);
                unset($routeconfig);
            }
        }
        return static::$router;
    }

    public static function interceptRawRequest(...$args) {
        call_user_func_array([Kernel::$interceptor, 'invokePre'], $args);
    }

    public static function interceptPostRequest(...$args) {
        call_user_func_array([Kernel::$interceptor, 'invokePost'], $args);
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
            throw new \Venn\Exception\ServiceNotFound("Service not registered", 404);
        }
        $loader = Kernel::$config["service"][$where];
        $loader['app_name'] = Kernel::$config['internal']['app_name'];
        $loader['app_version'] = Kernel::$config['internal']['app_version'];
        if (!empty($loader["loader"]) && is_callable('\\Venn\\service\\' . $loader["loader"] . "::init")) {
            return call_user_func('\\Venn\\service\\' . strtolower($loader["loader"]) . "::init", $loader["address"], $path, array_merge($loader, $what));
        } else {
            throw new \Venn\Exception\ServiceNotFound("Something went wrong", 501);
        }
    }

    public static function loadController($controller, $data) {
        if (empty($controller) || !is_string($controller)) {
            throw new \InvalidArgumentException("Provided controller must be a string");
        }
        if (0 === strpos($controller, "venn:")) {
            $callable = "Venn\\controller\\" . substr($controller, strlen("venn:")) . "::start";
        } elseif (strpos($controller, "\\app\\controller\\") !== 0) {
            $callable = "app\\controller\\$controller::start";
        } else {
            $callable = "$controller::start";
        }
        if (is_callable($callable)) {
            return call_user_func($callable, $data);
        } else {
            throw new \Venn\Exception\ControllerException("Controller, {$controller}, not found. Ensure controller name is properly specified!");
        }
    }

    public static function loadComponent($component, $data) {
        if (empty($component) || !is_string($component)) {
            throw new \InvalidArgumentException("Provided component must be a string");
        }
        if (0 === strpos($component, "venn:")) {
            $callable = "Venn\\component\\" . substr($component, strlen("venn:")) . "::start";
        } else if (strpos($component, "\\app\\component\\") === false) {
            $callable = "\\app\\component\\$component::start";
        } else {
            $callable = "$component::start";
        }
        if (is_callable($callable)) {
            $compinstance = call_user_func($callable, $data);
            if (!$compinstance instanceof \Venn\component\Component) {
                throw new \Venn\Exception\ComponentException("Provided Component '$component' must extend \Venn\component\Component class!");
            }
            return $compinstance;
        } else {
            throw new \Venn\Exception\ComponentException("Component '$component' not found. Ensure component name is properly specified!");
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
