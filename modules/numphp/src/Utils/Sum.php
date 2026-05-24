<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Sum
{
    public static function sum(...$args)
    {
        return \NumPHP\NumPHP::sum(...$args);
    }
}
