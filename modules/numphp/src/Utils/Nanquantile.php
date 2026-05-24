<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Nanquantile
{
    public static function nanquantile(...$args)
    {
        return \NumPHP\NumPHP::nanquantile(...$args);
    }
}
