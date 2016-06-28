<?php
namespace Dobest\View;
class View {
    public $view;
    public $data;
    // 1 - json
    // 2 - jsonp ?
    // 3 - php
    // 4 - twig
    public $type;
    public function __construct($view,$type)
    {
        $this->view = $view;
        $this->type = $type;
    }
    public static function make($viewName = null)
    {
        if ( !defined('VIEW_BASE_PATH') ) {
            throw new \InvalidArgumentException("VIEW_BASE_PATH is undefined!");
        }
        if ( ! $viewName ) {
            throw new \InvalidArgumentException("View name can not be empty!");
        }

        $templateName = str_replace('.', '/', $viewName);
        $pathNoExt = VIEW_BASE_PATH . '/' . $templateName;
        if ( is_file($pathNoExt.'.php') ) {
            return new View($pathNoExt.'.php',3);
        } else if( is_file($pathNoExt.'.twig') ) {
            $loader = new \Twig_Loader_Filesystem(VIEW_BASE_PATH);
            $twig = new \Twig_Environment($loader, array(
                'cache' => CACHE_BASE_PATH . '/twig/',
                'auto_reload' => true,
            ));
            return new View($twig->loadTemplate($templateName . ".twig"),4);
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
                echo json_encode($view->view);
            } else if ($view->type==3) { // php
                if ($view->data) {
                    extract($view->data);
                }
                require $view->view;
            } else if ($view->type==4) { // twig
                echo $view->view->render($view->data);
            }
        }
    }
    public function with($key, $value = null)
    {
        $this->data[$key] = $value;
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
