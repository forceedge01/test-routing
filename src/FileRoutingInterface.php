<?php

namespace Genesis\TestRouting;

use Traversable;

/**
 * Routing interface when working with files.
 */
interface FileRoutingInterface
{
    /**
     * @param string $filePath
     *
     * @return void
     */
    public static function registerFile($filePath);
}
