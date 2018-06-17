<?php

namespace Venn\request;

use Venn\core\Kernel;
use \Symfony\Component\Routing\Matcher\UrlMatcher;
use \Venn\core\event\EventEmitter;

/**
 * Used for routing requests
 *
 * @author LORD AGWA
 */
final class Router extends EventEmitter {

    public $routeFile, $routePath;
    protected $request;
    protected $routeLoader;
    const p = [1, 5, 5];

    public function __construct(route\RouteLoader $routeReference = null, array $context = []) {
        parent::__construct();
        if (empty($routeReference)) {
            // TODO: Set an error handler to execute if $routeReference is not provided!   
            throw new \UnexpectedValueException("{$routeReference} must not be null");
        }
        $this->routeLoader = $routeReference;
        $this->init($context);
    }

    private function init($context) {
        $this->routeLoader->setContext($context);
    }

    public function route($value = null) {
        $this->request = Request::create(filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_URL));
        // Paths should not contain double slashes!
        if (null === $this->request || stristr($this->request['path'], "//") !== false) {
            throw new \Venn\Exception\FileNotFound(null, 404);
        }
        try {
            $routes = $this->routeLoader->getRoutes($value);
            $routes->addPrefix($this->routeLoader->basePath);

            $matcher = new UrlMatcher($routes, $this->request->createRequestContext());
            $pre_resolved = $matcher->match($this->request['path']);

            if (is_array($pre_resolved)) {
                $this->request->resolve($routes->get($pre_resolved['_route']), $pre_resolved['_controller'], $this->inflateParams($pre_resolved));
                $this->emit(\Venn\core\event\Event::ROUTE_MATCHED);
                return true;
            } else {
                // Unexpected results!!
                throw new \Venn\Exception\FileNotFound("Not Found", 404);
            }
        } catch (\Venn\Exception\Basis $e) {
            //TODO: Handle error on either Kernel or calling component or a separate method
            switch (strtolower($e->getName())) {
                case "filenotfound":
                    header("HTTP/1.1 " . $e->getCode() . " " . $e->getMessage());
                    break;
                default :
                    // TODO: re-throw exception for handling at higher hierarchy
                    header("HTTP/1.1 404 " . $e->getMessage()); //TODO: remove message
                    break;
            }
        } catch (\Exception $e) {
            header("HTTP/1.1 404 {$e->getMessage()}"); //TODO: remove message
        }
        return false;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getLoader() {
        return $this->routeLoader;
    }

    private function inflateParams(array $params) {
        for ($i = 0; $i < count(route\Route::$RESERVED_DEFAULTS); $i++) {
            if (array_key_exists(route\Route::$RESERVED_DEFAULTS[$i], $params)) {
                unset($params[route\Route::$RESERVED_DEFAULTS[$i]]);
            }
        }
        return $params;
    }

}
