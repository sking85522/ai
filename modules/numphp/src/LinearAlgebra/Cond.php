<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Cond
{
    public static function cond(NDArray $a): float
    {
        $n1 = Norm::norm($a);
        $inv = Inverse::inverse($a);
        $n2 = Norm::norm($inv);
        return $n1 * $n2;
    }
}
