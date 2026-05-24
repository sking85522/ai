<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Fromiter
{
    public static function fromiter(...$args)
    {
        return \NumPHP\NumPHP::fromiter(...$args);
    }
}
