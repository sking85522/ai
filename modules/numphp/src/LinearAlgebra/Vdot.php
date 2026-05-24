<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Vdot
{
    public static function vdot(NDArray $a, NDArray $b): float
    {
        $x = Flatten::flatten($a)->getData();
        $y = Flatten::flatten($b)->getData();
        $n = min(count($x), count($y));
        $sum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $x[$i] * $y[$i];
        }
        return $sum;
    }
}
