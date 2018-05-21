<?php

namespace Venn\request\route;

//include '../../exception/functionexception.php';

final class Route {

    /**
     * This array contains:
     *  <ol>
     * <li>The unparsed url </li>
     * <li>An array from the url</li>
     * <li>Route parameters values with parameter names as keys
     *
     * @var array result that is passed on to a @see Ref::queue() responder callable
     */
    private static $result;
    private static $verb = null, $search = null;
    private static $base = [];
    private static $loader;

    const GET = "get", POST = "post", PUT = "put", PATCH = "patch", DELETE = "delete", HEAD = "head";
    const MINIMUM_ALLOWED_NUMERIC_PARAM = 2;
    const STATE_ROUTE_FOUND = 1, STATE_ROUTE_NOT_FOUND = -1;

    private static $acceptedVerbs = [Route::GET, Route::POST, Route::PUT, Route::PATCH, Route::DELETE, Route::HEAD];
    public static $state = Route::STATE_ROUTE_NOT_FOUND;
    // TODO: $request might be better left as private
    private static $requestParam = null;
    public static $request = null;

    private function __construct($url = null) {
        
    }

    /*
     * =======================================
     *                  SECTION
     * ========================================
     *  Methods for handling various kinds of http requests
     * 
     */

    public static function __callStatic($name, $arguments) {
        return Route::handleRequest($name, $arguments);
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

    private static function handleRequest($name, $arguments, $instance = null) {
                if (Route::$state == Route::STATE_ROUTE_FOUND) {
            return;
        }
        if (empty(Route::$requestParam)) {
            throw new \Venn\Exception\RouterException("The request must not be empty!");
        }

        if (in_array(strtolower($name), Route::$acceptedVerbs)) {
            if (Route::queue(strtolower($name), $arguments[0], $arguments[1])) {
                throw new \Venn\request\RouteFoundState();
            } else {
                return false;
            }
        } elseif (RouteFilter::hasModifier(strtolower($name))) {
            // return instance of Route
            return RouteFilter::modify($instance ? $instance : new Route(), strtolower($name), $arguments);
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
    private static function queue($verb, $pathToMatch, $responder) {
        if (Route::$requestParam->verb !== $verb) {//    #obvious
            return false;
        }
        if (!empty(static::$base['path'])) {
            $pathToMatch = static::$base['path'] . ltrim($pathToMatch, '/'); // trim provided url to remove leading slash
        }
        if (stristr(Route::$requestParam->path, "//") !== false || stristr($pathToMatch, "//") !== false) {
            throw new \Venn\Exception\FileNotFound(null, 403);
        }
        $path = trim($pathToMatch, "/");
        //Route::normalizePath($pathToMatch);
//        if (($param = stripos(Route::$request->path, '?')) !== false) {
//            $path .= substr(Route::$request->path, $param); // attach query parameters to path ***** subject to change =====> matching might be done without query params
//        }
        if (($one = stripos($path, "{")) !== false) {//    path contains placeholder
            if ((strlen(Route::$requestParam->path) > $one && $one > 0) && stripos(Route::$requestParam->path, "/", $one - 1) === $one - 1 && (Route::parse_path($path, Route::$requestParam->path))) {
//                exit(); //   do not exit script from here
                return Route::setRequest($path, $responder); //  return value is mostly ignored
            } else {
                return false;
            }
        } elseif (strcasecmp($path, Route::$requestParam->path) === 0) {
//            TODO: Add path to matched path variable------DONE!
            //======= params ======
            Route::$result = null;
            //=====================                          
//            exit(); //  do not exit script from here 
            return Route::setRequest($path, $responder); //  return value is mostly ignored
        } else {
            return false;
        }
    }

    private static function parse_path() {
        if (func_num_args() !== 2) {
//            throw new \Venn\Exception\FunctionException();  
            return false;
        }
        $board = [];
        foreach (func_get_args() as $i => $arg) {
            if ($i === 1 && isset(Route::$search)) {
                $board[$i] = Route::$search;
                break;
            }
            if (($parampos = stripos($arg, '?')) !== false) {
                $arg = substr($arg, 0, $parampos);
            }
            //            $initial = explode("/", trim($arg, "/")); // removes leading slashes
            $board[$i] = explode("/", $arg);  // $initial;
        }
        return Route::extractParameter($board);
    }

    private static function extractParameter(&$board) {
        if (count($board[0]) !== count($board[1]) && isset($board[0])) {
            return false;
        }
        for ($i = 0; $i < count($board[0]); $i++) {
            $pos1 = $pos2 = false;
            if (strcasecmp($board[0][$i], $board[1][$i]) !== 0 && (($pos1 = stripos($board[0][$i], "{")) === false || ($pos2 = stripos($board[0][$i], "}")) === false)) {
                return false;
            } // check if current string contains parameter, if it does not and it doesnt equal url, return false
            if ($pos1 === false || $pos2 === false) {
                continue;
            } // Checks if the current string is a parameter. If not, skip loop step
            if ($pos1 > 0 || ($pos2 < 1 && $pos2 !== (strlen($board[0][$i]) - 1))) {
                throw new \Venn\Exception\ParserException("Path parameters must start and end with braces. e.g. '/{param}/{here}'");
            }
            $name = substr($board[0][$i], $pos1 + 1, $pos2 - 1);
            if (is_numeric($name) && intval($name) < Route::MINIMUM_ALLOWED_NUMERIC_PARAM) {
                throw new \Venn\Exception\ParserException('You may only use numbers >=' . Route::MINIMUM_ALLOWED_NUMERIC_PARAM . ' as url parameter tokens!', \Venn\Exception\ParserException::INVALID_PARAMETER);
            }
            Route::$requestParam->addParam($name, $board[1][$i]);
        }
        Route::$search = $board[1];
//        $board[0] = Ref::$url;
        //======= params ======
        unset($board[0]);
        unset($board[1]);
        //=====================
        Route::$result = $board;
        return true;
    }

    private static function setRequest($path, $responder) {
        Route::$state = Route::STATE_ROUTE_FOUND;
        Route::$requestParam->matchedPath = $path;
//                exit(); //   do not exit script from here
        Route::$request = new \Venn\request\Request(Route::$requestParam, $responder);
        return true;
    }

    public static function setBase($path, $domain) {
        $base = [];
        if (!empty($path)) {
            if (stripos($path, '/', strlen($path) - 1) === false) {
                $path = $path . '/';
            }
            $base['path'] = $path;
        }
        if (!empty($domain)) {
            $base['domain'] = $domain;
        }
        static::$base = $base;
        return;
    }

    public static function &initRequestParams($url) {
        if (stristr($url, "//") !== false) {
            throw new \Venn\exception\FileNotFound(null, 403);
        }
        if (($param = stripos($url, '?')) !== false) {
            // attach query parameters to path ***** subject to change =====> matching might be done without query params 
            //### UPDATE ### ===> Mathing is done without query params.
            $url = strtok($url, '?');
        }
        //TODO: Decide whether to remove Ref static variables
        Route::$requestParam = new \Venn\request\RequestParameters(strtolower(filter_input(INPUT_SERVER, "REQUEST_METHOD")), strtolower(filter_input(INPUT_SERVER, "REQUEST_SCHEME", FILTER_SANITIZE_STRING)), filter_input(INPUT_SERVER, "HTTP_HOST", FILTER_SANITIZE_URL), rawurldecode(trim($url, '/')), getallheaders());
        Route::$requestParam->port = filter_input(INPUT_SERVER, "SERVER_PORT");
        Route::$requestParam->queryString = ($param = strtok(null)) ? $param : null;
        return Route::$requestParam;
    }

    private static function normalizePath($path) {
        
    }

}
