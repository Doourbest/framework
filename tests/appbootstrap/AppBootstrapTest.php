<?php

use PHPUnit\Framework\TestCase;
use Dobest\AppBootstrap as AppBootstrap;


class AppBootstrapTest extends TestCase
{
    function testBootstrap() {
        AppBootstrap::Bootstrap();
        // teset autoload
        $this->assertEquals(true,\App\Foo::foo());
    }
}

