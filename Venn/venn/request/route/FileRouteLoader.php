<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io> 2018
 *  Part of Venn
 */

namespace Venn\request\route;

/**
 * Description of FileRouteLoader
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
class FileRouteLoader extends RouteLoader {

    const ERR_CANNOT_LOAD_ROUTES = 2;

    protected $routePath, $routeFile;

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
     *              <code>user.first.donot.com</code>
     */
    public $baseDomain;

    public function __construct($routeReference = null) {
        if (empty($routeReference)) {
            // TODO: Set an error handler to execute if $routeReference is not provided!   
            throw new \UnexpectedValueException("{$routeReference} must not be null");
        }
        if (!is_readable($routeReference)) {
            throw new \Venn\Exception\RouterException("Provided route path must be path to a readable directory! {$routeReference}");
        }
        $this->routePath = $routeReference;
        $this->routes = new \Symfony\Component\Routing\RouteCollection();
    }

    protected function fetchRoutes() {
        include $this->routePath . DIRECTORY_SEPARATOR . $this->routeFile;
    }

    public function addRoute(\Symfony\Component\Routing\Route $route) {
        if (!empty($route->getHost())) {
            $route->setHost(trim($route->getHost(), '.') . '.' . trim($this->baseDomain));
        }
        $this->routes->add(Route::generateName($route), $route);
    }

    public function getRoutes($value) {
        $this->routeFile = !empty($value) && isset($value) ? strval($value) . "_routes.php" : 'routes.php';
        if (!is_readable($this->routePath . $this->routeFile)) {
            throw new \Venn\Exception\RouterException("Routes file does not exist, or is not readable!", self::ERR_CANNOT_LOAD_ROUTES);
        }
        $this->fetchRoutes();
        return $this->routes;
    }

    public function removeRoute($route) {
        
    }

    public function setRoutes($routes) {
        $this->routes = $routes;
    }

    public function setContext(array $context) {
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
    }

}
