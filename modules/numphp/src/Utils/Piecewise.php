<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Piecewise
{
    public static function piecewise(...$args)
    {
        return \NumPHP\NumPHP::piecewise(...$args);
    }
}
