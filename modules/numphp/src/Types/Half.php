<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Half
{
    public static function half(...$args)
    {
        return \NumPHP\NumPHP::half(...$args);
    }
}
