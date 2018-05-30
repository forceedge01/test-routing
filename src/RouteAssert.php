<?php

namespace Genesis\TestRouting;

use Exception;
use Genesis\TestRouting\Exception\QueryParamMismatchException;
use Genesis\TestRouting\Exception\RouteMismatchException;
use Genesis\TestRouting\Exception\RouteNotFoundException;
use Traversable;

class RouteAssert
{
    /**
     * @param string $expectedPage
     * @param string $currentUrl
     * @param callable $function
     *
     * @return void
     */
    public static function page(
        $expectedPage,
        $currentUrl,
        callable $function = null
    ) {
        $expectedUrl = Routing::getRoute($expectedPage, $function);

        self::uri($expectedUrl, $currentUrl);
    }

    /**
     * @param string $expectedUri
     * @param string $actualUri
     *
     * @return void
     */
    public static function uri($expectedUri, $actualUri)
    {
        $parsedActualUri = parse_url($actualUri);
        $parsedExpectedUri = parse_url($expectedUri);

        if ($parsedExpectedUri['path'] !== $parsedActualUri['path']) {
            throw new RouteMismatchException($parsedActualUri['path'], $parsedExpectedUri['path'], $actualUri);
        }

        if (isset($parsedActualUri['query'])) {
            $expectedQueryParams = [];
            $actualQueryParams = [];

            parse_str($parsedExpectedUri['query'], $expectedQueryParams);
            parse_str($parsedActualUri['query'], $actualQueryParams);

            self::queryParams($expectedQueryParams, $actualQueryParams);
        }
    }

    /**
     * @param array $expectedQueryParams
     * @param array $actualQueryParams
     *
     * @return void
     */
    public static function queryParams(
        array $expectedQueryParams,
        array $actualQueryParams
    ) {
        foreach ($expectedQueryParams as $expectedQueryParam => $value) {
            if (! isset($actualQueryParams[$expectedQueryParam])) {
                throw new Exception(
                    'Expected to have found query param "' . $expectedQueryParam . '" on current URL, not found. ' .
                    'Query params found: ' . print_r($actualQueryParams, true)
                );
            }

            if ($value !== '.*' && $actualQueryParams[$expectedQueryParam] !== $value) {
                throw new QueryParamMismatchException(
                    $value,
                    $actualQueryParams[$expectedQueryParam],
                    $expectedQueryParam,
                    $actualQueryParams
                );
            }
        }
    }
}
