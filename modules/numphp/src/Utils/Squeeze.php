<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Squeeze
{
    public static function squeeze(...$args)
    {
        return \NumPHP\NumPHP::squeeze(...$args);
    }
}
