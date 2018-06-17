<?php

namespace Venn\request\route;

use \Symfony\Component\Routing\Route as Route2;

//include '../../exception/functionexception.php';

final class Route {

    const ANY = "any", GET = "get", POST = "post", PUT = "put", PATCH = "patch", DELETE = "delete", HEAD = "head";

    public static $RESERVED_DEFAULTS = ['_controller', '_route'];
    private static $acceptedVerbs = [Route::ANY, Route::GET, Route::POST, Route::PUT, Route::PATCH, Route::DELETE, Route::HEAD];
    // TODO: $request might be better left as private
    private $route;

    private function __construct($path) {
        $this->route = new Route2($path);
    }

    /*
     * =======================================
     *                  SECTION
     * ========================================
     *  Methods for handling various kinds of http requests
     * 
     */

    public static function __callStatic($name, $arguments) {
        return Route::handleRequest($name, $arguments, null);
    }

    /*   public static function get($path, $responder) {//    remove callable parameter type hint
      if (empty(Route::$request)) {
      new Route(filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_URL));
      }
      if (Route::$request->verb !== Route::GET) {//    #obvious
      return;
      }
      return Route::queue(Route::GET, $path, $responder);
      }
     */

    public function __call($name, $arguments) {
        return Route::handleRequest($name, $arguments, $this);
    }

    private static function handleRequest($name, $arguments, self $instance = null) {
//                if (Route::$state == Route::STATE_ROUTE_FOUND) {
//            return;
//        }
        if (null === $instance) {
            $instance = new Route($arguments[0]);
        }
        if (in_array(strtolower($name), Route::$acceptedVerbs)) {
            return $instance->queue(strtolower($name), $arguments[0], $arguments[1]);
        } elseif (RouteFilter::hasModifier(strtolower($name))) {
            // return instance of Route
            return RouteFilter::modify($instance, strtolower($name), $arguments);
        } else {
            throw new \Venn\BadFunctionCallException("Function '{$name}' is not supported!");
        }
    }

    /**
     * This connects a path to a controller for a particular @see $verb and checks for parameters in @see $path
     * @param string $verb
     * @param string $pathToMatch
     * @param callable|\controller\Controller $responder
     * @return boolean
     * @throws \Venn\Exception\FileNotFound
     */
    private function queue($verb, $pathToMatch, $responder) {
        $this->route->setPath($this->normalizePath($pathToMatch)); //normalize!!!!!!
        $this->setMethods($verb);
        $this->route->addDefaults(['_controller' => $responder]);
        \Venn\core\Kernel::getRouter()->getLoader()->addRoute($this->route);
    }

    private function setMethods($verb) {
        $this->route->setMethods($verb === self::ANY ? [] : $verb);
    }

    public static function normalizePath($path) {
//        echo '/' . ltrim(rtrim(trim($path), '/') . '/', '/') . '<p>';
        return '/' . trim(trim($path), '/');
    }

    public static function generateName(Route2 $route) {
        return implode('_', $route->getSchemes()) . $route->getHost() . '_' . implode('_', $route->getMethods()) . '_' . strtr($route->getPath(), '/', '_') . implode('_', array_keys($route->getDefaults()));
    }

}
