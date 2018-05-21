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
class Request {
    private $requestParam;

    private $resolvedController;

    public function __construct(RequestParameters $requestParam, $controller) {
        $this->requestParam = $requestParam;
        $this->resolvedController = $controller;
    }
    public function getResolvedController() {
        return $this->resolvedController;
    }

    public function setResolvedController($controller) {
        $this->resolvedController = $controller;
        return $this;
    }

    public static function create($url) {
        if (strstr($url, "//") !== false) {
            throw new \Venn\exception\FileNotFound("Path should not contain double slashes!", 403);
        }
        
        if (parse_url($url) !== false) {
            //TODO: Decide whether to remove Ref static variables
        Route::$requestParam = new \Venn\request\RequestParameters(strtolower(filter_input(INPUT_SERVER, "REQUEST_METHOD")), strtolower(filter_input(INPUT_SERVER, "REQUEST_SCHEME", FILTER_SANITIZE_STRING)), filter_input(INPUT_SERVER, "HTTP_HOST", FILTER_SANITIZE_URL), rawurldecode(trim($url, '/')), getallheaders());
        Route::$requestParam->port = filter_input(INPUT_SERVER, "SERVER_PORT");
        Route::$requestParam->queryString = ($param = strtok(null)) ? $param : null;
        }
        
        return Route::$requestParam;
    }

}
