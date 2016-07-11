<?php

namespace Dobest;

class AppBootstrap{

    public static function Bootstrap() {
        self::AppAutoloadRegister();
    }

    public static function AppAutoloadRegister() {
        spl_autoload_register(function($class) {

            if(self::startsWith($class,"App\\")) {
                $path = BASE_PATH.DIRECTORY_SEPARATOR.str_replace("\\",DIRECTORY_SEPARATOR,$class).'.php';
                if(file_exists($path)) {
                    include $path;
                    return;
                }
            }

            if(self::endsWith($class,"Controller")) {
                $path = BASE_PATH.'/App/http/controllers/'.str_replace("\\",DIRECTORY_SEPARATOR,$class).'.php';
                if(file_exists($path)) {
                    include $path;
                    return;
                }
            }

            if(self::endsWith($class,"Model")) {
                $path = BASE_PATH.'/App/http/models/'.str_replace("\\",DIRECTORY_SEPARATOR,$class).'.php';
                if(file_exists($path)) {
                    include $path;
                    return;
                }
            }
        });
    }


    private static  function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    private static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

}

