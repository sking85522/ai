<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Amax
{
    public static function amax(...$args)
    {
        return \NumPHP\NumPHP::amax(...$args);
    }
}
