<?php

namespace Genesis\TestRouting\Exception;

use Exception;

/**
 * RouteMismatchException class.
 */
class RouteNotFoundException extends Exception
{
    /**
     * @param string $route
     * @param array $routes
     */
    public function __construct($route, array $routes = null)
    {
        parent::__construct("Route '$route' is not a registered route." .
            ($routes ? ' All registered routes: ' . print_r($routes, true) : null));
    }
}
