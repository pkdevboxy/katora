<?php

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $container = new Katora\Container(['one' => 1]);
        $container->set('two', 2);
        $container->set('three', function ()
        {
            return 'something';
        });
        $this->assertTrue($container->has('one'));
        $this->assertTrue($container->has('two'));
        $this->assertTrue($container->has('three'));
    }

    public function testDependencies()
    {
        $container = new Katora\Container();
        $container->set('sum1', function ()
        {
            return 5 + 4 + $this->get('one');
        });
        $container->set('sum2', function ()
        {
            return 5 + 4 + $this->get('one') + $this->get('two');
        });
        $container->set('one', 1);
        $container->set('two', 2);
        $this->assertEquals($container->get('sum1'), 10);
        $this->assertEquals($container->get('sum2'), 12);
    }

    public function testExtensions()
    {
        $container = new Katora\Container();
        $container->set('sum', function ()
        {
            return 5 + 4;
        });
        $container->extend('sum', function ($sum)
        {
            return $sum + 1;
        });
        $this->assertEquals($container->get('sum'), 10);
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
