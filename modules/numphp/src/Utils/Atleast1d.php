<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Atleast1d
{
    public static function atleast_1d(...$args)
    {
        return \NumPHP\NumPHP::atleast_1d(...$args);
    }
}
