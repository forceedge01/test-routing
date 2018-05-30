<?php

namespace Genesis\TestRouting;

use Traversable;

/**
 * Basic routing interface.
 */
interface RoutingInterface
{
    const CAMEL_CASE = 1;

    const SNAKE_CASE = 2;

    const PASCAL_CASE = 3;

    const NO_CHANGE = 4;

    /**
     * @param string $name
     * @param string $url
     */
    public static function addRoute($name, $url);

    /**
     * @param string $route
     * @param callable|null $function The resolved route will be passed in.
     *
     * @return string
     */
    public static function getRoute($name, callable $function = null);

    /**
     * @return string
     */
    public static function getRoutes();

    /**
     * @param string $url
     * @param array $queryParams
     * @param string $strategy
     *
     * @return string
     */
    public static function appendQueryParamToUrl(
        $url,
        array $queryParams,
        $strategy = RoutingInterface::CAMEL_CASE
    );

    /**
     * @param TableNode $queryParams
     *
     * @return string
     */
    public static function formQueryParamString(
        array $queryParams,
        $strategy = RoutingInterface::CAMEL_CASE
    );
}
