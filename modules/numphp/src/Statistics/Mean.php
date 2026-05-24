<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Mean
{
    /**
     * Compute the arithmetic mean along the specified axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float|NDArray
     */
    public static function mean(NDArray $a, ?int $axis = null)
    {
        $sum = Sum::sum($a, $axis);
        $shape = $a->getShape();

        // Case: Flattened mean
        if ($axis === null) {
            $count = array_product($shape);
            if ($count == 0) return 0.0;
            return $sum / $count;
        }

        // Case: 1D Array
        if (count($shape) === 1) {
            $count = $shape[0];
            if ($count == 0) return 0.0;
            return $sum / $count;
        }

        // Case: 2D Array
        if (count($shape) === 2) {
            $count = 1;
            if ($axis === 0) {
                $count = $shape[0]; // Number of rows (elements per column)
            } elseif ($axis === 1 || $axis === -1) {
                $count = $shape[1]; // Number of columns (elements per row)
            }

            // Element-wise division for the resulting NDArray from Sum
            $sumData = $sum->getData();
            $resultData = array_map(function($val) use ($count) {
                return $val / $count;
            }, $sumData);

            return new NDArray($resultData, $a->dtype());
        }

        return $sum; // Fallback
    }
}