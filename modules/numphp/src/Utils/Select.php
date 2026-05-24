<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Select
{
    public static function select(...$args)
    {
        return \NumPHP\NumPHP::select(...$args);
    }
}
