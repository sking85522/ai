<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Ufunc
{
    public static function ufunc(...$args)
    {
        return \NumPHP\NumPHP::ufunc(...$args);
    }
}
