<?php

use PHPUnit\Framework\TestCase;
use Dobest\Support\Config;

class ConfigTest extends TestCase
{
    public function testInitConfig()
    {
        $config = [
            'host' => '127.0.0.1',
            'user' => 'mark',
            'password' => '122',
        ];
        Config::initConfig($config);
        $this->assertEquals($config, Config::all());
    }

    public function testGet()
    {
        $config = [
            'host' => '127.0.0.1',
            'user' => 'mark',
            'password' => '122',
        ];
        Config::initConfig($config);
        $this->assertEquals($config['user'], Config::get('user'));
    }

    public function testGetDefault()
    {
        $config = [
            'host' => '127.0.0.1',
            'user' => 'mark',
            'password' => '122',
        ];
        Config::initConfig($config);
        $this->assertEquals('echo', Config::get('name', 'echo'));
    }

    public function testHas()
    {
        $config = [
            'host' => '127.0.0.1',
            'user' => 'mark',
            'password' => '122',
        ];
        Config::initConfig($config);
        $this->assertEquals(true, Config::has('user'));
        $this->assertEquals(false, Config::has('name'));
    }

    public function testSet()
    {
        $config = [
            'host' => '127.0.0.1',
            'user' => 'mark',
            'password' => '122',
        ];
        Config::initConfig($config);
        Config::set('user','echo');
        $this->assertEquals('echo', Config::get('user'));
    }
}