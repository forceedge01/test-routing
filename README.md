Test Routing [ ![Codeship Status for forceedge01/test-routing](https://app.codeship.com/projects/2ff83fb0-1a47-0136-0f11-2a3b31bf5093/status?branch=master)](https://app.codeship.com/projects/284254)
============

Simple routing to use named pages instead of urls.

Features:
- ::getRoute() accepts a callback function that allows manipulation of url before returning final url.
- ::setAllRoutesFromExternalSource() provides a bridge for other routing mechanisms in place. You can re-use your existing application routing configuration.

Release detail:
---------------
Major: Released first version of test routing.
Minor: Throw custom exception when route not found displaying routes available.
Patch: NA

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
use Genesis\TestRouting\Routing;
use MyApp\Routing\AppRouter;

$router = new AppRouter($routes);
// As long as it is an iterable, it will do.
$routes = $router->getAll();

Routing::setAllRoutesFromExternalSource($routes, function ($route) {
    // $route contains individual elements contained within routes. Just return the name and the url.
    return [$route->getName() => $route->getUrl()];
});

Note: You can bypass the above by using the file data directly most of the time.
```

Integrating with the behat-sql-extension
----------------------------------------

```php
use Genesis\TestRouting\Routing;

$url = Routing::getRoute($pageName, function ($url) use ($sqlApi) {
    // Parse out any database keywords with their values from the keystore for dynamic URLs.
    return $sqlApi->get('keyStore')->parseKeywordsInString($url);
});
```
