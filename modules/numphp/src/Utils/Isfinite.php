<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Isfinite
{
    public static function isfinite(...$args)
    {
        return \NumPHP\NumPHP::isfinite(...$args);
    }
}
