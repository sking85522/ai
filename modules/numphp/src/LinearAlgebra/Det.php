<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Det
{
    public static function det(NDArray $a): float
    {
        return Determinant::det($a);
    }
}
