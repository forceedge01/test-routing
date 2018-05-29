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
}
