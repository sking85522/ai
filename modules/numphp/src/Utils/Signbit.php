<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Signbit
{
    public static function signbit(...$args)
    {
        return \NumPHP\NumPHP::signbit(...$args);
    }
}
