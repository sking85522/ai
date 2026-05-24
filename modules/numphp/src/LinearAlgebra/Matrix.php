<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Matrix
{
    public static function matrix(...$args)
    {
        return \NumPHP\NumPHP::matrix(...$args);
    }
}
