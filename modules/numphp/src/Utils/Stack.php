<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Stack
{
    public static function stack(...$args)
    {
        return \NumPHP\NumPHP::stack(...$args);
    }
}
