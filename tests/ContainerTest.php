<?php

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $container = new Katora\Container(array('one' => 1));
        $container->add('two', 2);
        $container->factory('three', function ()
        {
            return 'something';
        });
        $this->assertTrue($container->has('one'));
        $this->assertTrue($container->has('two'));
        $this->assertTrue($container->has('three'));
    }

    public function testDepends()
    {
        $container = new Katora\Container();
        $container->factory('sum1', function ($one)
        {
            return 5 + 4 + $one;
        }, 'one');
        $container->factory('sum2', function ($one, $two)
        {
            return 5 + 4 + $one + $two;
        }, array('one', 'two'));
        $container->add('one', 1);
        $container->add('two', 2);
        $this->assertEquals($container->get('sum1'), 10);
        $this->assertEquals($container->get('sum2'), 12);
    }

    public function testExtensions()
    {
        $container = new Katora\Container();
        $container->factory('sum', function ()
        {
            return 5 + 4;
        });
        $container->extend('sum', function ($sum)
        {
            return $sum + 1;
        });
        $this->assertEquals($container->get('sum'), 10);
    }

    public function testFactories()
    {
        $container = new Katora\Container();
        $container->factory('closure', function ()
        {
            return mt_rand(999, 9999);
        });
        $v1 = $container->get('closure');
        $v2 = $container->get('closure');
        $this->assertNotEquals($v1, $v2);
    }

    public function testSingleton()
    {
        $container = new Katora\Container();
        $container->singleton('instance', function ()
        {
            return mt_rand(999, 9999);
        });
        $v1 = $container->get('instance');
        $v2 = $container->get('instance');
        $this->assertEquals($v1, $v2);
    }
}
