Test Routing
============

Simple routing to use named pages instead of urls.

Features:
- ::getRoute() accepts a callback function that allows manipulation of url before returning final url.
- ::setAllRoutesFromExternalSource() provides a bridge for other routing mechanisms in place. You can re-use your existing application routing configuration.

Adding a route
---------------

```php
use Genesis\TestRouting\Routing;

Routing::addRoute('home page', '/home');
```

Get a route back
----------------

```php
use Genesis\TestRouting\Routing;

$route = Routing::getRoute('home page');

Output: /home
```

More advanced form of get
-------------------------

```php
use Genesis\TestRouting\Routing;

$route = Routing::getRoute('home page', function ($url) {
    return $url . '?testing=1';
});

Output: /home?testing=1
```

Re-using your application routing configuration
-----------------------------------------------

```php
$router = new AppRouter($routes);
// As long as it is an iterable, it will do.
$routes = $router->getAll();

Routing::setAllRoutesFromExternalSource($routes, function ($route) {
    // $route contains individual elements contained within routes. Just return the name and the url.
    return [$route->getName() => $route->getUrl()];
});

Note: You can bypass the above by using the file data directly most of the time.
```
