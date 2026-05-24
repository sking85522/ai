<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Complex128
{
    public static function complex128(...$args)
    {
        return \NumPHP\NumPHP::complex128(...$args);
    }
}
