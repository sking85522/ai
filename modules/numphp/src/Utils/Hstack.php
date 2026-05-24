<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Hstack
{
    public static function hstack(...$args)
    {
        return \NumPHP\NumPHP::hstack(...$args);
    }
}
