<?php

namespace NumPHP\Math\Calculus;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Trapezoid
{
    /**
     * Integrate along the given axis using the composite trapezoidal rule.
     *
     * @param NDArray $y
     * @param NDArray|null $x
     * @param float $dx
     * @return float
     */
    public static function trapezoid(NDArray $y, ?NDArray $x = null, float $dx = 1.0): float
    {
        $y_data = Flatten::flatten($y)->getData();
        $x_data = $x ? Flatten::flatten($x)->getData() : null;
        $sum = 0.0;

        for ($i = 0; $i < count($y_data) - 1; $i++) {
            $h = $x_data ? ($x_data[$i+1] - $x_data[$i]) : $dx;
            $sum += ($y_data[$i] + $y_data[$i+1]) * $h / 2.0;
        }

        return $sum;
    }
}