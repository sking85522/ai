<?php

namespace NumPHP\Math\Comparison;

use NumPHP\Core\NDArray;
use NumPHP\Math\Logical\All;

class AllClose
{
    /**
     * Returns True if two arrays are element-wise equal within a tolerance.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @param float $rtol
     * @param float $atol
     * @param bool $equal_nan
     * @return bool
     */
    public static function allclose(NDArray $a, NDArray $b, float $rtol = 1e-05, float $atol = 1e-08, bool $equal_nan = false): bool
    {
        $close_array = IsClose::isclose($a, $b, $rtol, $atol, $equal_nan);
        return All::all($close_array);
    }
}