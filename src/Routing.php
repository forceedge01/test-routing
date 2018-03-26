<?php

namespace Genesis\TestRouting;

use Traversable;

/**
 * Routing class. A simple class, this simplicity is to be retained for the purposes of bridging other
 * routing systems with this one.
 */
class Routing implements RoutingInterface
{
    /**
     * @var string[]
     */
    private static $routes;

    /**
     * @param string $name
     * @param string $url
     */
    public static function addRoute($name, $url)
    {
        self::$routes[$name] = $url;
    }

    /**
     * @param string $route
     * @param callback|null $function The resolved route will be passed in.
     *
     * @return string
     */
    public static function getRoute($name, callback $function = null)
    {
        if (! isset($routes[$name])) {
            throw new Exception("Route '$name' not found.");
        }

        if ($callback) {
            return $callback(self::$routes[$name]);
        }

        return self::$routes[$name];
    }

    /**
     * @return string
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Use to map your existing routing mechanism into the test routing mechanism by providing a transformation
     * callback.
     *
     * @param Traversable $routes
     * @param callback $transformationCallback Will receive the contained items one by one in routes. This
     * should return an array [$nameOfRoute, $url]
     */
    public static function setAllRoutesFromExternalSource(Traversable $routes, callback $transformationCallback)
    {
        foreach ($routes as $route) {
            list($name, $url) = $transformationCallback($route);

            self::$routes[$name] = $url;
        }
    }
}
