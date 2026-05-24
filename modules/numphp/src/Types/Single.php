<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Single
{
    public static function single(...$args)
    {
        return \NumPHP\NumPHP::single(...$args);
    }
}
