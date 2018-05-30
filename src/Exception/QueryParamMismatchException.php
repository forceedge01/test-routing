<?php

namespace Genesis\TestRouting\Exception;

use Exception;

/**
 * QueryParamMismatchException class.
 */
class QueryParamMismatchException extends Exception
{
    /**
     * @param string $expected
     * @param string $actual
     * @param string $queryParam
     * @param array $allActual
     */
    public function __construct($expected, $actual, $queryParam, array $allActual)
    {
        parent::__construct(
            "Query param '$queryParam' value did not match. + Actual, - Expected" .
            PHP_EOL . PHP_EOL .
            "+ $actual" .
            PHP_EOL .
            "- $expected" .
            PHP_EOL .
            PHP_EOL .
            'All values found: ' .
            print_r($allActual, true)
        );
    }
}
