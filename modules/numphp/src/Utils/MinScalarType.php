<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class MinScalarType
{
    public static function min_scalar_type(...$args)
    {
        return \NumPHP\NumPHP::min_scalar_type(...$args);
    }
}
