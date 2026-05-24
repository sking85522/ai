<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanmax
{
    public static function nanmax(...$args)
    {
        return \NumPHP\NumPHP::nanmax(...$args);
    }
}
