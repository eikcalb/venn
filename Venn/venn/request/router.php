<?php

namespace Venn\request;

use Venn\core\Kernel;

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
     *              <code>user.first.donot.com</code>
     */
    public $baseDomain;
    protected $request;
    protected $routes;

    public function __construct($routeReference = null, $context = []) {
        if (empty($routeReference)) {
            // TODO: Set an error handler to execute if $routeReference is not provided!   
            throw new \UnexpectedValueException("{$routeReference} must not be null");
        }
        if (empty($routeReference) || !is_readable($routeReference)) {
            throw new \Venn\Exception\RouterException("Provided route path must be path to a readable directory! $routeReference");
        }
        $this->routePath = $routeReference;
        $this->routes = new \Symfony\Component\Routing\RouteCollection();
        $this->init($context);
    }

    private function init($context) {
        if (array_key_exists("path", $context)) {
            $this->basePath = $context['path'];
        } else {
            $this->basePath = '';
        }
        $this->routes->addPrefix($this->basePath);

        if (array_key_exists("domain", $context)) {
            $this->baseDomain = $context['domain'];
        } else {
            $this->baseDomain = '';
        }
        $this->routes->setHost(".*\.{$this->baseDomain}");
        route\Route::setBase($this->basePath, $this->baseDomain);
    }

    public function route($value = null) {
        $this->routeFile = !empty($value) && isset($value) ? strval($value) . "_routes.php" : 'routes.php';
        if (!is_readable($this->routePath . $this->routeFile)) {
            throw new \Venn\Exception\RouterException("Routes file does not exist, or is not readable!");
        }

        $this->request = Request::create(filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_URL));

        try {
            /**
             * import routes file and check if @see route\Route::$state changes
             */
            new route\RouteLoader($this->routePath . $this->routeFile);
            if (route\Route::$state !== route\Route::STATE_ROUTE_FOUND || empty(route\Route::$request)) {
                throw new \Venn\Exception\FileNotFound("Not Found", 404);
            }
        } catch (\Venn\Exception\Basis $e) {
            //TODO: Handle error on either Kernel or calling component or a separate method
            switch (strtolower($e->getName())) {
                case RouteFoundState::NAME:
                    $this->call_controller(route\Route::$request->getResolvedController());
                    break;
                case "filenotfound":
                    header("HTTP/1.1 " . $e->getCode() . " " . $e->getMessage());
                    break;
                default :
                    // TODO: re-throw exception for handling at higher hierarchy
                    header("HTTP/1.1 404 " . $e->getMessage()); //TODO: remove message
                    break;
            }
        }
        $requestContext = new \Symfony\Component\Routing\RequestContext();
        $matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($this->routes, $requestContext);
        $matcher->match($pathinfo);
    }

    /**
     * 
     * @param type $controller <p>The action designated for the detected url</p>
     *                          <p>This function is called with the HTTP Method and @see Ref::queue results</p>
     * 
     */
    private function call_controller($controller) {
        if (empty($controller)) {
            throw new \Venn\Exception\ControllerException("Controller must be set for handling routes!");
        }
        if (is_callable($controller)) {
            $controller($this->request);
        } elseif (is_string($controller)) {
            Kernel::loadController($controller, $this->request);
        }
    }

}
