Test Routing
============

Simple routing to use named pages instead of urls.

Features:
- ::getRoute() accepts a callback function that allows manipulation of url before returning final url.
- ::setAllRoutesFromExternalSource() provides a bridge for other routing mechanisms in place. You can re-use your existing application routing configuration.

Adding a route:

```
use Genesis\TestRouting\Routing;

Routing::addRoute('home page', '/home');
```

Get a route back

```
use Genesis\TestRouting\Routing;

$route = Routing::getRoute('home page');

Output: /home
```

More advanced form of get

```
use Genesis\TestRouting\Routing;

$route = Routing::getRoute('home page', function ($url) {
    return $url . '?testing=1';
});

Output: /home?testing=1
```
