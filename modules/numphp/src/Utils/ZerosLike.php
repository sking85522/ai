<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class ZerosLike
{
    public static function zeros_like(...$args)
    {
        return \NumPHP\NumPHP::zeros_like(...$args);
    }
}
