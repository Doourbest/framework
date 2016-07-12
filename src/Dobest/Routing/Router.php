<?php
namespace Dobest\Routing;
/**
 * @method static Router get(string $route, Callable $callback)
 * @method static Router post(string $route, Callable $callback)
 * @method static Router put(string $route, Callable $callback)
 * @method static Router delete(string $route, Callable $callback)
 * @method static Router options(string $route, Callable $callback)
 * @method static Router head(string $route, Callable $callback)
 */
class Router {

    public static $filterRoutes = array();
    public static $filterCallbacks = array();

    public static $routes = array();
    public static $methods = array();
    public static $callbacks = array();
    public static $patterns = array(
        ':any' => '([^/]+)',
        ':num' => '([0-9]+)',
        ':all' => '(.*)'
    );
    public static $error_callback;

    /**
     * add filter for your routes
     */
    public static function filter($uri, $callback) {
        array_push(self::$filterRoutes, $uri);
        array_push(self::$filterCallbacks, $callback);
    }

    /**
     * Defines a route w/ callback and method
     */
    public static function __callstatic($method, $params)
    {
        $uri = $params[0];
        $callback = $params[1];
        if ( $method == 'any' ) {
            self::pushToArray($uri, 'get', $callback);
            self::pushToArray($uri, 'post', $callback);
        } else {
            self::pushToArray($uri, $method, $callback);
        }
    }
    /**
     * Push route items to class arrays
     *
     */
    public static function pushToArray($uri, $method, $callback)
    {
        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }
    /**
     * Defines callback if route is not found
    */
    public static function error($callback)
    {
        self::$error_callback = $callback;
    }

    public static function dispatch($after = null) {

        $filterHandlers = array();
        self::getFilterHandlers($filterHandlers);
        foreach($filterHandlers as $handler) {
            $ret = self::callUserHandler($handler);
            if($ret===false) {
                return false;         // 如果返回 false，终止整个 dispatch
            }
        }

        if(self::getMatchHandler($handler, $params)==false) {
            if (!self::$error_callback) {
                self::$error_callback = function() {
                    header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
                    echo '404';
                };
            }
            $ret = call_user_func(self::$error_callback);
        } else { 
            $ret = self::callUserHandler($handler,$params);
        }

        if ($after) {
            $segments = explode('@', $after);
            $afterClassName = $segments[0];
            $afterFunctionName = $segments[1];
            $afterClassName::$afterFunctionName($ret);
        }

    }

    private static function callUserHandler($handler,$params = array()) {
        if(!is_object($handler)){
            // format: "ClassName@MethodName"
            $segments = explode('@',$handler);
            $obj = new $segments[0]();
            $methodName = $segments[1];
            return call_user_func_array(array($obj,$methodName),$params);
        } else {
            return call_user_func_array($handler, $params);
        }
    }

    public static function getFilterHandlers(&$handlers) {

        $uri         = self::detect_uri();
        // check if route is defined without regex
        if ($routePos = array_keys(self::$filterRoutes, $uri)) {
            foreach ($routePos as $pos) {
                $handler = self::$filterCallbacks[$pos];
                $handlers[] = $handler;
            }
        }

        // check if defined with regex
        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);
        $cnt = count(self::$filterRoutes);
        for ($pos = 0; $pos<$cnt; ++$pos) {
            $route = self::$filterRoutes[$pos];
            if (strpos($route, ':') === false) {
                continue; // NOTICE: non regex pattern, this has been checked before
            }
            $route = str_replace($searches, $replaces, $route);
            if (preg_match('#^' . $route . '$#', $uri, $matches)) {
                array_shift($matches); // $matches[0] will contain the text that matched the full pattern, remove it
                $handler = self::$filterCallbacks[$pos];
                $handlers[] = $handler;
            }
        }

    }

    private static function getMatchHandler(&$handler, &$params) {
        $uri         = self::detect_uri();
        $method      = $_SERVER['REQUEST_METHOD'];
        // check if route is defined without regex
        if ($routePos = array_keys(self::$routes, $uri)) {
            foreach ($routePos as $pos) {
                if (self::$methods[$pos] == $method) {
                    $handler = self::$callbacks[$pos];
                    $params = array();
                    return true;
                }
            }
        } else {
            // check if defined with regex
            $searches = array_keys(static::$patterns);
            $replaces = array_values(static::$patterns);
            $cnt = count(self::$routes);
            for ($pos = 0; $pos<$cnt; ++$pos) {
                $route = self::$routes[$pos];
                if (strpos($route, ':') === false) {
                    continue; // NOTICE: non regex pattern, this has been checked before
                }
                $route = str_replace($searches, $replaces, $route);
                if (preg_match('#^' . $route . '$#', $uri, $matches)) {
                    if (self::$methods[$pos] == $method) {
                        array_shift($matches); // $matches[0] will contain the text that matched the full pattern, remove it
                        $handler = self::$callbacks[$pos];
                        $params = $matches;
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // detect true URI, inspired by CodeIgniter 2
    private static function detect_uri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($uri == '' || $uri=='/') {
            return '/';
        }
        return str_replace(array('//', '../'), '/', rtrim($uri, '/'));

    }
}
