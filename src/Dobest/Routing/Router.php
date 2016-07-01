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
    public static function filter($filter, $result) {
        if ($filter()) {
            $result();
        }
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
   
        if(self::getMatchHandler($handler, $params)==false) {
            if (!self::$error_callback) {
                self::$error_callback = function() {
                    header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
                    echo '404';
                };
            }
            call_user_func(self::$error_callback);
            return;
        }

        if(!is_object($handler)){
            // format: "ClassName@MethodName"
            $segments = explode('@',$handler);
            $obj = new $segments[0]();
            $methodName = $segments[1];
            $return = call_user_func_array(array($obj,$methodName),$params);
        } else {
            $return = call_user_func_array($handler, $params);
        }

        if ($after) {
            $segments = explode('@', $after);
            $afterClassName = $segments[0];
            $afterFunctionName = $segments[1];
            $afterClassName::$afterFunctionName($return);
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
                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }
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
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }
        if ($uri == '/' || empty($uri)) {
            return '/';
        }
        $uri = parse_url($uri, PHP_URL_PATH);
        return str_replace(array('//', '../'), '/', rtrim($uri, '/'));
    }
}
