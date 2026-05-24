<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class CanCast
{
    public static function can_cast(...$args)
    {
        return \NumPHP\NumPHP::can_cast(...$args);
    }
}
