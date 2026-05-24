<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Random
{
    public static function random(...$args)
    {
        return \NumPHP\NumPHP::random(...$args);
    }
}
