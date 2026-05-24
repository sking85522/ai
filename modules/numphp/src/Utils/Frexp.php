<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Frexp
{
    public static function frexp(...$args)
    {
        return \NumPHP\NumPHP::frexp(...$args);
    }
}
