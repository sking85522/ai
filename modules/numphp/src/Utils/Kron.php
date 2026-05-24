<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Kron
{
    public static function kron(...$args)
    {
        return \NumPHP\NumPHP::kron(...$args);
    }
}
