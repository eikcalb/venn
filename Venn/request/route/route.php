<?php

namespace request\route;

//include '../../exception/functionexception.php';
use core\Kernel;

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
    private static $request = null;
    private static $verb = null, $search = null;
    private static $base = [];

    const GET = "get", POST = "post", PUT = "put", PATCH = "patch", DELETE = "delete", HEAD = "head";
    const MINIMUM_ALLOWED_NUMERIC_PARAM = 2;

    private static $acceptedVerbs = [Route::GET, Route::POST, Route::PUT, Route::PATCH, Route::DELETE, Route::HEAD];
    public static $state = false;

    private function __construct($url) {
        
    }

    /*
     * =======================================
     *                  SECTION
     * ========================================
     *  Methods for handling various kinds of http requests
     * 
     */

    public static function __callStatic($name, $arguments) {
        if (empty(Route::$request)) {
            throw new \Exception\RouterException("The request must not be empty!");
        }

        if (in_array(strtolower($name), Route::$acceptedVerbs)) {
            return Route::queue(strtolower($name), $arguments[0], $arguments[1]);
        } elseif (in_array(strtolower($name), RouteModifier::$acceptedModifiers)) {
            
        } else {
            throw new \BadFunctionCallException("Function '{$name}' is not supported!");
        }
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
        if (empty(Route::$request)) {
            throw new \Exception\RouterException("The request must not be empty!");
        }

        if (in_array(strtolower($name), Route::$acceptedVerbs)) {
            return Route::queue(strtolower($name), $arguments[0], $arguments[1]);
        } elseif (in_array(strtolower($name), Route::$acceptedModifiers)) {
            
        } else {
            throw new \BadFunctionCallException("Function '{$name}' is not supported!");
        }
    }

    /**
     * This connects a path to a controller for a particular @see $verb and checks for parameters in @see $path
     * @param string $verb
     * @param string $pathToMatch
     * @param callable|\controller\Controller $responder
     * @return boolean
     * @throws \Exception\FileNotFound
     */
    private static function queue($verb, $pathToMatch, $responder) {
        if (!empty(static::$base['path'])) {
            $pathToMatch = static::$base['path'] . substr($pathToMatch, 1);
        }
        if (Route::$request->verb !== $verb) {//    #obvious
            return false;
        }
        if (stristr(Route::$request->path, "//") !== false || stristr($pathToMatch, "//") !== false) {
            throw new \Exception\FileNotFound(null, 403);
        }
        $path = trim($pathToMatch, "/");
//        if (($param = stripos(Route::$request->path, '?')) !== false) {
//            $path .= substr(Route::$request->path, $param); // attach query parameters to path ***** subject to change =====> matching might be done without query params
//        }
        if (($one = stripos($path, "{")) !== false) {//    path contains placeholder
            if ((strlen(Route::$request->path) > $one && $one > 0) && stripos(Route::$request->path, "/", $one - 1) === $one - 1 && (Route::parse_path($path, Route::$request->path))) {
                Route::$state = true;
                Route::$request->matchedPath = $path;
                Route::call_controller($responder);
//                exit(); //   do not exit script from here
                return true; //  return value is mostly ignored
            } else {
                return false;
            }
        } elseif (strcasecmp($path, Route::$request->path) === 0) {
            Route::$state = true;
//            Ref::$result[0] = Ref::$url;
            //======= params ======
            Route::$result = null;
            //=====================                          
            Route::call_controller($responder);
//            exit(); //  do not exit script from here 
            return true;
        }
    }

    private static function parse_path() {
        if (func_num_args() !== 2) {
//            throw new \Exception\FunctionException();  
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
                throw new \Exception\ParserException("Path parameters must start and end with braces. e.g. '/{param}/{here}'");
            }
            $name = substr($board[0][$i], $pos1 + 1, $pos2 - 1);
            if (is_numeric($name) && intval($name) < Route::MINIMUM_ALLOWED_NUMERIC_PARAM) {
                throw new \Exception\ParserException('You may only use numbers >=' . Route::MINIMUM_ALLOWED_NUMERIC_PARAM . ' as url parameter tokens!', \Exception\ParserException::INVALID_PARAMETER);
            }
            Route::$request->addParam($name, $board[1][$i]);
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

    public static function initRequestParams($url) {
        if (stristr($url, "//") !== false) {
            throw new \exception\FileNotFound(null, 403);
        }
        if (($param = stripos($url, '?')) !== false) {
            // attach query parameters to path ***** subject to change =====> matching might be done without query params 
            //### UPDATE ### ===> Mathing is done without query params.
            $url = strtok($url, '?');
        }
        //TODO: Decide whether to remove Ref static variables
        Route::$request = new \request\RequestParameters(strtolower(filter_input(INPUT_SERVER, "REQUEST_METHOD")), strtolower(filter_input(INPUT_SERVER, "REQUEST_SCHEME", FILTER_SANITIZE_STRING)), filter_input(INPUT_SERVER, "HTTP_HOST", FILTER_SANITIZE_URL), trim($url, '/'), getallheaders());
        Route::$request->queryString = strtok(null);
        return Route::$request;
    }

    /**
     * 
     * @param type $controller <p>The action designated for the detected url</p>
     *                          <p>This function is called with the HTTP Method and @see Ref::queue results</p>
     * 
     */
    private static function call_controller($controller) {
        if (empty($controller)) {
            throw new \Exception\ControllerException("Controller must be set for handling routes!");
        }

        $request = Route::$request;

        if (is_callable($controller)) {
            $controller($request);
        } elseif (is_string($controller)) {
            Kernel::loadController($controller, $request);
        }
    }

}
