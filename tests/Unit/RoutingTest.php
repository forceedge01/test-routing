<?php

namespace Genesis\TestRouting\Tests;

use Genesis\TestRouting\Routing;
use Genesis\TestRouting\RoutingInterface;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class RoutingTest extends PHPUnit_Framework_TestCase
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
        $this->reflection = new ReflectionClass(Routing::class);
        $this->testObject = $this->reflection->newInstanceArgs($this->dependencies);
    }

    /**
     * @expectedException Exception
     */
    public function testRegisterFileNotFound()
    {
        Routing::registerFile('/Config/abc.php');
    }

    public function testRegisterFileFound()
    {
        Routing::registerFile(__DIR__ . '/../bootstrap.php');
    }

    /**
     * @expectedException Exception
     */
    public function testNoRoute()
    {
        $route = 'test route';

        Routing::getRoute($route);
    }

    /**
     * @runInSeparateProcess
     */
    public function testAddRoute()
    {
        $route = 'abc page';
        $url = '/abc/123/';

        Routing::addRoute($route, $url);

        $routes = $this->getStaticPropertyValue('routes');

        $this->assertEquals([$route => $url], $routes);
    }

    public function testGetRoute()
    {
        $this->setStaticPropertyValue('routes', [
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/987/'
        ]);

        $url = Routing::getRoute('abc page');

        self::assertEquals('/abc/123/', $url);
    }

    public function testGetRouteCallback()
    {
        $this->setStaticPropertyValue('routes', [
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/987/'
        ]);

        $url = Routing::getRoute('abc page', function ($url) {
            return str_replace('abc', 'kkk', $url);
        });

        self::assertEquals('/kkk/123/', $url);
    }

    /**
     * @expectedException Genesis\TestRouting\Exception\RouteNotFoundException
     */
    public function testGetRouteExceptionWhenNotFound()
    {
        $this->setStaticPropertyValue('routes', [
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/987/'
        ]);

        Routing::getRoute('banana page');
    }

    public function testAddRoutes()
    {
        $this->setStaticPropertyValue('routes', [
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/987/'
        ]);

        Routing::addRoutes([
            'hhh' => '/hhh/777/'
        ]);

        $routes = $this->getStaticPropertyValue('routes');

        self::assertEquals([
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/987/',
            'hhh' => '/hhh/777/'
        ], $routes);
    }

    public function testGetRoutes()
    {
        $this->setStaticPropertyValue('routes', [
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/987/'
        ]);

        $routes = Routing::getRoutes();

        self::assertEquals([
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/987/'
        ], $routes);
    }

    public function testSetAllRoutesFromExternalSource()
    {
        $routes = [
            [
                'name' => 'abc page',
                'url' => '/abc/123/',
                'method' => 'post'
            ], [
                'name' => 'xyz page',
                'url' => '/xyz/123/',
                'method' => 'get'
            ]
        ];

        Routing::setAllRoutesFromExternalSource($routes, function ($route) {
            return [$route['name'], $route['url']];
        });

        $routes = $this->getStaticPropertyValue('routes');

        self::assertEquals([
            'abc page' => '/abc/123/',
            'xyz page' => '/xyz/123/'
        ], $routes);
    }

    public function testFormQueryParamStringDefaultStrategy()
    {
        $queryParam = Routing::formQueryParamString([
            'booking Id' => 123,
            'User Id' => 888
        ]);

        self::assertEquals('bookingId=123&userId=888', $queryParam);
    }

    public function testFormQueryParamStringCamelCaseStrategy()
    {
        $queryParam = Routing::formQueryParamString([
            'booking Id' => 123,
            'User Id' => 888
        ], RoutingInterface::CAMEL_CASE);

        self::assertEquals('bookingId=123&userId=888', $queryParam);
    }

    public function testFormQueryParamStringSnakeCaseStrategy()
    {
        $queryParam = Routing::formQueryParamString([
            'booking Id' => 123,
            'User Id' => 888
        ], RoutingInterface::SNAKE_CASE);

        self::assertEquals('booking_id=123&user_id=888', $queryParam);
    }

    public function testFormQueryParamStringPascalCaseStrategy()
    {
        $queryParam = Routing::formQueryParamString([
            'booking Id' => 123,
            'User Id' => 888
        ], RoutingInterface::PASCAL_CASE);

        self::assertEquals('BookingId=123&UserId=888', $queryParam);
    }

    public function testFormQueryParamStringNoChangeCaseStrategy()
    {
        $queryParam = Routing::formQueryParamString([
            'booking Id' => 123,
            'User Id' => 888
        ], RoutingInterface::NO_CHANGE);

        self::assertEquals('booking+Id=123&User+Id=888', $queryParam);
    }

    public function testAppendQueryParamsToUrlStrategy()
    {
        $url = '/booking/';
        $queryParams = ['booking id' => 55, 'user ID' => 99];

        $fullUrl = Routing::appendQueryParamToUrl($url, $queryParams, RoutingInterface::PASCAL_CASE);

        self::assertEquals('/booking/?BookingId=55&UserID=99', $fullUrl);
    }

    public function testAppendQueryParamsToUrlStrategyWithExistingQueryParams()
    {
        $url = '/booking/?abc=1';
        $queryParams = ['booking id' => 55, 'user ID' => 99];

        $fullUrl = Routing::appendQueryParamToUrl($url, $queryParams, RoutingInterface::SNAKE_CASE);

        self::assertEquals('/booking/?abc=1&booking_id=55&user_id=99', $fullUrl);
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    private function getStaticPropertyValue($property)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->testObject);
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    private function setStaticPropertyValue($property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($this->testObject, $value);

        return $this;
    }
}
