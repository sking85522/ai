<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Transpose
{
    public static function transpose(...$args)
    {
        return \NumPHP\NumPHP::transpose(...$args);
    }
}
