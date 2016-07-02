
<?php

use PHPUnit\Framework\TestCase;
use Dobest\Routing\Router as Router;

class RouterTest extends TestCase
{

    static $result = "";

    static function storeResult($data) {
        self::$result = $data;
    }

    public function testGet() {
        $msg = "Hello world";

        Router::get('/test_get', function() use ($msg) {
            return $msg;
        });

        $_SERVER['REQUEST_METHOD']  = "GET";
        $_SERVER['REQUEST_URI'] = "/test_get";
        Router::dispatch('RouterTest@storeResult');
        $this->assertEquals($msg,self::$result);
    }

    public function testPost() {
        $msg = "Hello world";

        Router::post('/test_post', function() use ($msg) {
            return $msg;
        });

        $_SERVER['REQUEST_METHOD']  = "POST";
        $_SERVER['REQUEST_URI'] = "/test_post";
        Router::dispatch('RouterTest@storeResult');
        $this->assertEquals($msg,self::$result);
    }

    public function testNotFound() {
        $msg = "Hello world, testNotFound";

        Router::get('/test_not_found', function() use ($msg) {
            return $msg;
        });

        $_SERVER['REQUEST_METHOD']  = "POST";
        $_SERVER['REQUEST_URI'] = "/test_not_found";
        $errorMsg = "";
        Router::error(function() use (&$errorMsg) {
            $errorMsg = "oh no 404";
        });
        Router::dispatch('RouterTest@storeResult');
        $this->assertEquals($errorMsg,"oh no 404");
    }

    public function testPattern() {
        $msg = "Hello world, testNotFound";

        Router::get('/test_pattern/:any/:num', function($param1, $param2) {
            return array($param1, $param2);
        });

        $param1 = "param1";
        $param2 = "12345";

        $_SERVER['REQUEST_METHOD']  = "GET";
        $_SERVER['REQUEST_URI'] = "/test_pattern/$param1/$param2";
        $errorMsg = "";
        Router::dispatch('RouterTest@storeResult');
        $this->assertEquals(array($param1,$param2),self::$result);

        $param1 = "param1";
        $param2 = "abcde";      // not digit
        $_SERVER['REQUEST_METHOD']  = "GET";
        $_SERVER['REQUEST_URI'] = "/test_pattern/$param1/$param2";
        $errorMsg = "";
        Router::error(function() use (&$errorMsg) {
            $errorMsg = "oh no 404";
        });
        Router::dispatch('RouterTest@storeResult');
        $this->assertEquals("oh no 404",$errorMsg);
    }

    public function testController() {
        Router::get('/test_controller', "TestController@handler");
        $_SERVER['REQUEST_METHOD']  = "GET";
        $_SERVER['REQUEST_URI'] = "/test_controller";
        Router::dispatch('RouterTest@storeResult');
        $this->assertEquals(TestController::$result, self::$result);
    }

}

class TestController {
    static $result = "handler";
    public function handler() {
        return self::$result;
    }
}

