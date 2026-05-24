<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Float128
{
    public static function float128(...$args)
    {
        return \NumPHP\NumPHP::float128(...$args);
    }
}
