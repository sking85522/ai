<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Matvec
{
    public static function matvec(...$args)
    {
        return \NumPHP\NumPHP::matvec(...$args);
    }
}
