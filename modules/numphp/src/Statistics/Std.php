<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\Math\Basic\Sqrt;

class Std
{
    /**
     * Computes the standard deviation of an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float|NDArray
     */
    public static function std(NDArray $a, ?int $axis = null)
    {
        $variance = Var_::var($a, $axis);
        if ($variance instanceof NDArray) {
            return Sqrt::sqrt($variance);
        }
        return sqrt($variance);
    }
}