<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Complex64
{
    public static function complex64(...$args)
    {
        return \NumPHP\NumPHP::complex64(...$args);
    }
}
