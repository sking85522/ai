<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Shape
{
    public static function shape(...$args)
    {
        return \NumPHP\NumPHP::shape(...$args);
    }
}
