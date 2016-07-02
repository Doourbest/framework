<?php

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {

    public function testPhpView() {
        ob_start();
        $name = "Template engine.";
        $user = array("name"=>"roxma");
        $view = Dobest\View\View::make("phptemplate");
        $view->with("name",$name);
        $view->with("user",$user);
        Dobest\View\View::process($view);
        $assertStr = "Hello, This is a $name. user.name={$user['name']}, enjoy!\n";
        $this->assertEquals($assertStr, str_replace("\r","\n",str_replace("\r\n","\n",ob_get_clean())));
    }

    public function testTwigView() {
        ob_start();
        $name = "Template engine.";
        $user = array("name"=>"roxma");
        $view = Dobest\View\View::make("twigtemplate");
        $view->with("name",$name);
        $view->with("user",$user);
        Dobest\View\View::process($view);
        $assertStr = "Hello, This is a $name. user.name={$user['name']}, enjoy!\n";
        $this->assertEquals($assertStr, str_replace("\r","\n",str_replace("\r\n","\n",ob_get_clean())));
    }

    public function testBladeView() {

        // singleton
        $files      = new Illuminate\Filesystem\Filesystem();

        // singleton
        $blade      = new  Illuminate\View\Compilers\BladeCompiler($files,__DIR__.'/blade');

        $finder     = new Illuminate\View\FileViewFinder($files,array(__DIR__));
        $events     = new \Illuminate\Events\Dispatcher(); // container ? what the hell!??

        $resolver     = new Illuminate\View\Engines\EngineResolver();
        $resolver->register('blade', function () use ($blade) {
            return new Illuminate\View\Engines\CompilerEngine($blade);
        }); 

        $factory    = new Illuminate\View\Factory($resolver, $finder, $events);

        $result = $factory->make('bladetemplate')->with('name','roxma')->render();

        // 兼容 windos 和 mac 的换行
        $result = str_replace("\r","\n",str_replace("\r\n","\n",$result));

        $this->assertEquals("begin[ Hello world!!! My name is roxma!\n ]end\n", "$result");
    }
}

