<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanargmax
{
    public static function nanargmax(...$args)
    {
        return \NumPHP\NumPHP::nanargmax(...$args);
    }
}
