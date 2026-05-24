<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class __loader__
{
    public static function __loader__(...$args)
    {
        return \NumPHP\NumPHP::__loader__(...$args);
    }
}
