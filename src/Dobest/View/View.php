<?php
namespace Dobest\View;
class View {
    public $view;
    public $data;
    // 1 - json
    // 2 - jsonp ?
    // 3 - php
    // 4 - twig
    // 5 - blade
    public $type;
    public function __construct($view,$type)
    {
        $this->view = $view;
        $this->type = $type;
        $this->data = array();
    }

    private static function stringEndsWith($whole, $end) {
            return (strpos($whole, $end, strlen($whole) - strlen($end)) !== false);
    }

    public static function make($viewName = null)
    {
        if ( !defined('VIEW_BASE_PATH') ) {
            throw new \InvalidArgumentException("VIEW_BASE_PATH is undefined!");
        }
        if ( ! $viewName ) {
            throw new \InvalidArgumentException("View name can not be empty!");
        }

        $viewPath = VIEW_BASE_PATH . '/' . $viewName;
        $isFile   = is_file($viewPath);

        if (is_file($viewPath . '.blade.php')) {
            $files    = new \Illuminate\Filesystem\Filesystem();                     // singleton
            $blade    = new \Illuminate\View\Compilers\BladeCompiler($files, CACHE_BASE_PATH); // singleton
            $finder   = new \Illuminate\View\FileViewFinder($files,array(VIEW_BASE_PATH));
            $events   = new \Illuminate\Events\Dispatcher(); // container ? what the hell!??
            $resolver = new \Illuminate\View\Engines\EngineResolver();
            $resolver->register('blade', function () use ($blade) {
                return new \Illuminate\View\Engines\CompilerEngine($blade);
            }); 
            $factory    = new \Illuminate\View\Factory($resolver, $finder, $events);
            return new View($factory->make($viewName),5);
        } else if( $isFile && (self::stringEndsWith($viewPath,'.twig.php')||self::stringEndsWith($viewPath,'.twig.html')) ) {
            $loader = new \Twig_Loader_Filesystem(VIEW_BASE_PATH);
            $twig = new \Twig_Environment($loader, array(
                'cache' => CACHE_BASE_PATH . '/twig/',
                'auto_reload' => true,
            ));
            return new View($twig->loadTemplate($viewName),4);
        } else if ( $isFile && self::stringEndsWith($viewPath,'.php') ) {
            return new View($viewPath,3);
        } else {
            throw new \UnexpectedValueException("View file does not exist!");
        }
    }

    public static function json($arr)
    {
        if ( !is_array($arr) ) {
            throw new \UnexpectedValueException("View::json can only recieve Array!");
        } else {
            return new View($arr, 1);
        }
    }
    public static function process($view = null)
    {
        if ( is_string($view) ) {
            echo $view;
            return;
        }
        if ( $view instanceof View ) {
            if ($view->type == 1) { // json
                header('Content-type: application/json');
                echo json_encode($view->view);
            } else if ($view->type==3) { // php
                if ($view->data) {
                    extract($view->data);
                }
                require $view->view;
            } else if ($view->type==4) { // twig
                echo $view->view->render($view->data);
            } else if ($view->type==5) { // blade
                echo $view->view->with($view->data)->render();
            }
        }
    }
    public function with($key, $value = null)
    {
        if(is_array($key)) {
            $this->data = array_merge($this->data,$key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'with'))
        {
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }
        throw new \BadMethodCallException("Function [$method] does not exist!");
    }
}
