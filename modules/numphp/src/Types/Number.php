<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Number
{
    public static function number(...$args)
    {
        return \NumPHP\NumPHP::number(...$args);
    }
}
