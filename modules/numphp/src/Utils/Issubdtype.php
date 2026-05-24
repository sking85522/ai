<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Issubdtype
{
    public static function issubdtype(...$args)
    {
        return \NumPHP\NumPHP::issubdtype(...$args);
    }
}
