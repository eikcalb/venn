<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io>
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Venn\request;

/**
 *  Encapsulates details of the current request, including; @see RequestParameters and resolved route controllers (@see route\Route)
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
class Request implements \ArrayAccess {

    private $requestParam;
    private $resolvedController;
    private $resolvedRoute;
    public static $current;

    public function __construct(RequestParameters $requestParam, \Venn\controller\Controller $controller = null) {
        $this->requestParam = $requestParam;
        $this->resolvedController = $controller;
        self::$current = &$this;
    }

    public function getResolvedController() {
        return $this->resolvedController;
    }

    public function &getParameters() {
        return $this->requestParam;
    }

    public function setParameters(RequestParameters $params) {
        $this->requestParam = $params;
    }

    protected function setRouteParameters(array $params) {
        return $this->requestParam->addParams($params);
    }

    protected function setResolvedController($controller) {
        if (empty($controller)) {
            throw new \Venn\Exception\RequestException("Controller cannot be empty");
        }
        $this->resolvedController = \Venn\controller\Controller::call_controller($controller, $this->requestParam);
        return $this;
    }

    protected function setResolvedRoute($route) {
        if (empty($route)) {
            throw new \Venn\Exception\RequestException("Route cannot be empty");
        }
        $this->resolvedRoute = $route;
        return $this;
    }

    public function resolve($route, $controller, $params = []) {
        $this->setResolvedRoute($route)->setRouteParameters($params);
        $this->setResolvedController($controller);
        return $this;
    }

    public function createRequestContext() {
//        return new \Symfony\Component\Routing\RequestContext('', $this->requestParam->verb, $this->requestParam->host, $this->requestParam->protocol, $this->isSecure() ? 80 : $this->requestParam->port, $this->isSecure() ? $this->requestParam->port : 443, $this->requestParam->path, $this->requestParam->queryString);
        return new \Symfony\Component\Routing\RequestContext('', $this->requestParam->verb, $this->requestParam->host, $this->requestParam->protocol, $this->isSecure() ? 80 : $this->requestParam->port, $this->isSecure() ? $this->requestParam->port : 443, $this->requestParam->path, $this->requestParam->queryString);
    }

    public function isSecure() {
        return 'https' === strtolower($this->requestParam->protocol);
    }

    public static function create($url) {
        if (strstr($url, "//") !== false) {
            throw new \Venn\exception\FileNotFound("Path should not contain double slashes!", 403);
        }
        if (false !== $url2 = parse_url($url)) {
            $requestParam = new \Venn\request\RequestParameters(
                    strtolower(filter_input(INPUT_SERVER, "REQUEST_METHOD")), strtolower(filter_input(INPUT_SERVER, "REQUEST_SCHEME", FILTER_SANITIZE_STRING)),
                    //TODO: Change 'HTTP_HOST' $_server variable to 'SERVER_NAME'
                    filter_input(INPUT_SERVER, "HTTP_HOST", FILTER_SANITIZE_URL), rawurldecode(route\Route::normalizePath($url2['path'])), getallheaders());
            $requestParam->port = (int) filter_input(INPUT_SERVER, "SERVER_PORT");
            $requestParam->queryString = !empty($url2['query']) ? rawurldecode($url2['query']) : '';
            return new self($requestParam);
        }
        return null;
    }

    public function offsetExists($offset) {
        return isset($this->requestParam->{$offset});
    }

    public function offsetGet($offset) {
        $this->requestParam->{$offset};
        return $this->requestParam->{$offset};
    }

    public function offsetSet($offset, $value) {
        $this->requestParam->$$offset = $value;
    }

    public function offsetUnset($offset) {
        unset($this->requestParam->$$offset);
    }

}
