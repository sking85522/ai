<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;
use NumPHP\Math\Basic\Power;
use NumPHP\Math\Basic\Subtract;

class Var_
{
    /**
     * Computes the variance of an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float|NDArray
     */
    public static function var(NDArray $a, ?int $axis = null)
    {
        if ($axis === null) {
            $flattened = Flatten::flatten($a)->getData();
            if (empty($flattened)) {
                return 0.0;
            }
            $mean = array_sum($flattened) / count($flattened);
            $variance = 0;
            foreach ($flattened as $value) {
                $variance += pow($value - $mean, 2);
            }
            return $variance / count($flattened);
        }

        // Using formula: E[X^2] - (E[X])^2 for axis-based calculation
        $mean_a = Mean::mean($a, $axis);
        $a_squared = Power::power($a, 2);
        $mean_of_squares = Mean::mean($a_squared, $axis);

        if ($mean_a instanceof NDArray) {
            $mean_a_squared = Power::power($mean_a, 2);
            return Subtract::subtract($mean_of_squares, $mean_a_squared);
        } else {
            $mean_a_squared = pow($mean_a, 2);
            // mean_of_squares will also be a float in this case
            return $mean_of_squares - $mean_a_squared;
        }
    }
}