<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Flexible
{
    public static function flexible(...$args)
    {
        return \NumPHP\NumPHP::flexible(...$args);
    }
}
