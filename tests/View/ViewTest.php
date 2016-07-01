<?php

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testPhpView()
    {
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

    public function testTwigView()
    {
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
}

