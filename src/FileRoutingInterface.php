<?php

namespace Genesis\TestRouting;

use Traversable;

/**
 * Routing class. A simple class, this simplicity is to be retained for the purposes of bridging other
 * routing systems with this one.
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
