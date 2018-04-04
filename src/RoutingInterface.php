<?php

namespace Genesis\TestRouting;

use Traversable;

/**
 * Routing class. A simple class, this simplicity is to be retained for the purposes of bridging other
 * routing systems with this one.
 */
interface RoutingInterface
{
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
     * Use to map your existing routing mechanism into the test routing mechanism by providing a transformation
     * callable.
     *
     * @param iterable $routes
     * @param callable $transformationcallable Will receive the contained items one by one in routes. This
     * should return an array [$nameOfRoute, $url]
     */
    public static function setAllRoutesFromExternalSource($routes, callable $transformationcallable);
}
