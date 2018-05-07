<?php

namespace request;

/**
 * Used for routing requests
 *
 * @author LORD AGWA
 */
final class Router {
    
    public $routeFile, $routePath;

    /**
     * @var string Base path upon which route urls are matched. This is used to abstract
     *              routes based on path. The matched path is ignored from paths to be routed.
     *              e.g. suppose a route handles <code>GET /java</code> the @see Router#$basePath can be 
     *              set to <code>from/home/to/</code> then the route will be launched for a request to
     *              <code>from/home/to/java</code>
     */
    public $basePath;

    /**
     *
     * @var string Base domain name upon which route urls are matched. This is used to abstract
     *              routes based on subdomain. The matched domain is ignored from hostname to be routed.
     *              e.g. suppose a route handles <code>GET user.first</code> the @see Router#$baseDomain can be 
     *              set to <code>donot.com</code> then the route will be launched for a request to
     *              <code>from/home/to/java</code>
     */
    public $baseDomain;
    protected $request;

    public function __construct($routeReference = null, $context = []) {
        if (empty($routeReference)) {
            $routeReference = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "router" . DIRECTORY_SEPARATOR;
        }
        if (empty($routeReference) || !is_readable($routeReference)) {
            throw new \Exception\RouterException("Provided route path must be path to a readable directory!");
        }
        $this->routePath = $routeReference;
        $this->init($context);
    }
    
    private function init($context) {
        if (array_key_exists("path", $context)) {
            $this->basePath = $context['path'];
        } else {
            $this->basePath = '';
        }
        if (array_key_exists("domain", $context)) {
            $this->baseDomain = $context['domain'];
        } else {
            $this->baseDomain = '';
        }
        route\Route::setBase($this->basePath, $this->baseDomain);
    }

    public function route($value = null) {
        $this->routeFile = !empty($value) && isset($value) ? strval($value) . "_routes.php" : 'routes.php';
        if (!is_readable($this->routePath . $this->routeFile)) {
            throw new \Exception\RouterException("Routes file does not exist, or is not readable!");
        }

        $this->request = route\Route::initRequestParams(filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_URL));

        try {
            /*
             * import routes file
             */
            while (!route\Route::$state) {
                require $this->routePath . $this->routeFile;
                if (!route\Route::$state) {
                    throw new \Exception\FileNotFound("Not Found", 404);
                }
            }
        } catch (\Exception\Basis $e) {
            //TODO: Handle error on either Kernel or calling component or a separate method
            switch (strtolower($e->getName())) {
                case "filenotfound":
                    header("HTTP/1.1 " . $e->getCode() . " " . $e->getMessage());
                    break;
                default :
                    header("HTTP/1.1 404 " . $e->getMessage()); //TODO: remove message
                    break;
            }
        }
    }

}
