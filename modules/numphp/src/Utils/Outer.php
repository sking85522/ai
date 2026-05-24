<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Outer
{
    public static function outer(...$args)
    {
        return \NumPHP\NumPHP::outer(...$args);
    }
}
