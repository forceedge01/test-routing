<?php

namespace Genesis\TestRouting\Tests;

use Genesis\TestRouting\RouteAssert;
use Genesis\TestRouting\Routing;
use Genesis\TestRouting\RoutingInterface;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class RouteAssertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RoutingInterface The object to be tested.
     */
    private $testObject;

    /**
     * @var ReflectionClass The reflection class.
     */
    private $reflection;

    /**
     * @var array The test object dependencies.
     */
    private $dependencies = [];

    /**
     * Set up the testing object.
     */
    public function setUp()
    {
        Routing::addRoute('Hello World', '/hello-world/');
        Routing::addRoute('Hello World 2', '/hello-world-2/');

        $this->reflection = new ReflectionClass(RouteAssert::class);
        $this->testObject = $this->reflection->newInstanceArgs($this->dependencies);
    }

    public function testPage()
    {
        $page = 'Hello World';
        $url = '/hello-world/';

        RouteAssert::page($page, $url);
    }

    public function testPageWithCallback()
    {
        $page = 'Hello World';
        $url = '/hello-world/?bookingId=54';

        RouteAssert::page($page, $url, function ($url) {
            return $url . '?bookingId=54';
        });
    }

    /**
     * @expectedException Exception
     */
    public function testPageNotCorrectPage()
    {
        $page = 'Hello World 2';
        $url = '/hello-world/';

        RouteAssert::page($page, $url);
    }

    public function testUriQueryParams()
    {
        $expectedUri = '/hello-world-3/?abc=1&xyz=3';
        $actualUri = '/hello-world-3/?abc=1&xyz=3';

        RouteAssert::uri($expectedUri, $actualUri);
    }

    /**
     * @expectedException Exception
     */
    public function testUriQueryParamsWrong()
    {
        $expectedUri = '/hello-world-3/?abc=1&xyz=388888';
        $actualUri = '/hello-world-3/?abc=1&xyz=3';

        RouteAssert::uri($expectedUri, $actualUri);
    }

    public function testAssertQueryParamsAllGood()
    {
        $expected = ['bookingId' => 55, 'userId' => 77];
        $actual = ['bookingId' => 55, 'userId' => 77];

        RouteAssert::queryParams($expected, $actual);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage banana
     */
    public function testAssertQueryParamsNotFound()
    {
        $expected = ['bookingId' => 55, 'banana' => 77];
        $actual = ['bookingId' => 55, 'userId' => 77];

        RouteAssert::queryParams($expected, $actual);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage 99
     */
    public function testAssertQueryParamsValueMismatch()
    {
        $expected = ['bookingId' => 55, 'userId' => 99];
        $actual = ['bookingId' => 55, 'userId' => 77];

        RouteAssert::queryParams($expected, $actual);
    }
}
