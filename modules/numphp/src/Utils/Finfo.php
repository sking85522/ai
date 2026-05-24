<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Finfo
{
    public static function finfo(...$args)
    {
        return \NumPHP\NumPHP::finfo(...$args);
    }
}
