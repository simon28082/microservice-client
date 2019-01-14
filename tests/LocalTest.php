<?php

namespace CrCms\Microservice\Client\Tests;

use CrCms\Microservice\Client\Services\Local;
use PHPUnit\Framework\TestCase;

/**
 * Class LocalTest
 * @package CrCms\Microservice\Client\Tests
 */
class LocalTest extends TestCase
{

    public function testLocal1()
    {
        $app = \Mockery::mock('Illuminate\Contracts\Container\Container');
        $config = global_config();
        $config['connections']['local']['discover']['path'] = __DIR__.'/local1-service.json';
        $local = new Local($app, $config);
        $testings = $local->services('testing');
        $this->assertEquals(0,$testings[0]['id']);
        $this->assertEquals('testing',$testings[0]['name']);
        $this->assertEquals($config['default_port'],$testings[0]['port']);
        $this->assertEquals('192.168.1.1',$testings[0]['host']);
    }


    public function testLocal2()
    {
        $app = \Mockery::mock('Illuminate\Contracts\Container\Container');
        $config = global_config();
        $config['connections']['local']['discover']['path'] = __DIR__.'/local2-service.json';
        $local = new Local($app, $config);
        $testings = $local->services('testing');
        $this->assertEquals('testing_1',$testings[0]['id']);
        $this->assertEquals('testing',$testings[0]['name']);
        $this->assertEquals($config['default_port'],$testings[0]['port']);
        $this->assertEquals('192.168.1.1',$testings[0]['host']);
    }

}