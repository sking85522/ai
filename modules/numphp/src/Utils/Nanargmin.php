<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanargmin
{
    public static function nanargmin(...$args)
    {
        return \NumPHP\NumPHP::nanargmin(...$args);
    }
}
