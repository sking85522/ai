<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Isscalar
{
    public static function isscalar(...$args)
    {
        return \NumPHP\NumPHP::isscalar(...$args);
    }
}
