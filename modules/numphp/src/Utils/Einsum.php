<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Einsum
{
    public static function einsum(...$args)
    {
        return \NumPHP\NumPHP::einsum(...$args);
    }
}
