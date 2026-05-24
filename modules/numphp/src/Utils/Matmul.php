<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class Matmul
{
    public static function matmul(...$args)
    {
        return \NumPHP\NumPHP::matmul(...$args);
    }
}
