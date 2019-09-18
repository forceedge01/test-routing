<?php

namespace Genesis\TestRouting;

use Exception;
use Genesis\TestRouting\Exception\RouteNotFoundException;

/**
 * Routing class. A simple class, this simplicity is to be retained for the purposes of bridging other
 * routing systems with this one.
 */
class Routing implements RoutingInterface, FileRoutingInterface, ExtendedRoutingInterface
{
    /**
     * @var string[]
     */
    private static $routes;

    /**
     * @param string $filePath
     *
     * @return void
     */
    public static function registerFile($filePath)
    {
        if (! file_exists($filePath)) {
            throw new Exception("File to be registered '$filePath' not found.");
        }

        require $filePath;
    }

    /**
     * @param string $name
     * @param string $url
     */
    public static function addRoute($name, $url)
    {
        self::$routes[$name] = $url;
    }

    /**
     * Checks that the given url matches the registered route.
     *
     * @param string $name
     * @param string $url
     *
     * @return boolean
     */
    public static function isRoute($name, $url)
    {
        $parsedUrl = parse_url($url);

        if (self::getRoute($name) === $parsedUrl['path']) {
            return true;
        }

        return false;
    }

    /**
     * Checks that the given url matches the registered route.
     *
     * @param array  $name
     * @param string $url
     *
     * @return boolean
     */
    public static function isInRoutes(array $names, $url)
    {
        $parsedUrl = parse_url($url);

        $urls = [];
        foreach ($names as $name) {
            $urls[] = self::getRoute($name);
        }

        if (array_search($parsedUrl['path'], $urls) !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param string $url
     *
     * @return boolean
     */
    public static function matchesRoute($name, $url)
    {
        $parsedUrl = parse_url($url);

        if (strpos(self::getRoute($name), $parsedUrl['path']) !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param string        $route
     * @param callable|null $function The resolved route will be passed in.
     * @param mixed         $name
     *
     * @return string
     */
    public static function getRoute($name, callable $function = null)
    {
        if (! isset(self::$routes[$name])) {
            if (! self::$routes) {
                throw new Exception('No routes registered, please register before calling on routes.');
            }

            throw new RouteNotFoundException($name, array_keys(self::$routes));
        }

        if ($function) {
            return $function(self::$routes[$name]);
        }

        return self::$routes[$name];
    }

    /**
     * @param array $routes
     */
    public static function addRoutes(array $routes)
    {
        foreach ($routes as $name => $url) {
            self::addRoute($name, $url);
        }
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
     * @param iterable $routes
     * @param callback $transformationCallback Will receive the contained items one by one in routes. This
     *                                         should return an array [$nameOfRoute, $url]
     */
    public static function setAllRoutesFromExternalSource($routes, callable $transformationCallback)
    {
        foreach ($routes as $index => $route) {
            list($name, $url) = $transformationCallback($route, $index);

            self::$routes[$name] = $url;
        }
    }

    /**
     * @param TableNode $queryParams
     * @param mixed     $strategy
     *
     * @return string
     */
    public static function formQueryParamString(
        array $queryParams,
        $strategy = RoutingInterface::CAMEL_CASE
    ) {
        $formattedQueryParams = [];
        foreach ($queryParams as $key => $item) {
            $formattedQueryParams[self::convert($key, $strategy)] = $item;
        }

        return http_build_query($formattedQueryParams);
    }

    /**
     * @param string $url
     * @param array  $queryParams
     * @param string $strategy
     *
     * @return string
     */
    public static function appendQueryParamToUrl(
        $url,
        array $queryParams,
        $strategy = RoutingInterface::CAMEL_CASE
    ) {
        return $url .
            (false === strpos($url, '?') ? '?' : '&' ) .
            self::formQueryParamString($queryParams, $strategy);
    }

    /**
     * @param string $key
     * @param string $strategy
     *
     * @return string
     */
    private static function convert($key, $strategy)
    {
        switch ($strategy) {
            case RoutingInterface::CAMEL_CASE:
                return str_replace(' ', '', lcfirst(ucwords($key)));

            case RoutingInterface::SNAKE_CASE:
                return str_replace(' ', '_', strtolower($key));

            case RoutingInterface::PASCAL_CASE:
                return str_replace(' ', '', ucwords($key));

            default:
                return $key;
        }
    }
}
