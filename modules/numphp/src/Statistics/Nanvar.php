<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanvar
{
    /**
     * Compute the variance along the specified axis, while ignoring NaNs.
     *
     * @param NDArray $a
     * @return float
     */
    public static function nanvar(NDArray $a): float
    {
        $mean = Nanmean::nanmean($a);
        if (is_nan($mean)) {
            return NAN;
        }

        $data = Flatten::flatten($a)->getData();
        $filtered = array_filter($data, function($val) { return !is_nan($val); });

        if (count($filtered) < 1) {
            return NAN;
        }

        $sumOfSquares = array_sum(array_map(function($val) use ($mean) { return pow($val - $mean, 2); }, $filtered));

        return $sumOfSquares / count($filtered);
    }
}