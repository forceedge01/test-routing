<?php

namespace Genesis\TestRouting\Exception;

use Exception;
use QuickPack\Base\Context\FailureContext;

/**
 * RouteMismatchException class.
 */
class RouteMismatchException extends Exception
{
    public function __construct($actualUrl, $expectedUrl, $fullUrl = null)
    {
        parent::__construct(self::provideDiff(
            $expectedUrl,
            $actualUrl,
            'Url does not match.' . ($fullUrl ? ' Full actual URL: ' . $fullUrl : null)
        ));
    }

    /**
     * @param string $expected
     * @param string $actual
     * @param string $message
     *
     * @return string
     */
    private function provideDiff($expected, $actual, $message)
    {
        return 'Mismatch: (+ expected, - actual)' . PHP_EOL . PHP_EOL .
            '+ ' . $expected . PHP_EOL .
            '- ' . $actual . PHP_EOL . PHP_EOL .
            'Info: ' . $message;
    }
}
