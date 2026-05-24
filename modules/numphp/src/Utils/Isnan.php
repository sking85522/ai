<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Isnan
{
    public static function isnan(...$args)
    {
        return \NumPHP\NumPHP::isnan(...$args);
    }
}
