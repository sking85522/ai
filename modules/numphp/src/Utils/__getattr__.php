<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class __getattr__
{
    public static function __getattr__(...$args)
    {
        return \NumPHP\NumPHP::__getattr__(...$args);
    }
}
