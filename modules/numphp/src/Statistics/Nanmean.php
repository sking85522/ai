<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanmean
{
    /**
     * Compute the arithmetic mean along the specified axis, ignoring NaNs.
     *
     * @param NDArray $a
     * @return float
     */
    public static function nanmean(NDArray $a): float
    {
        $data = Flatten::flatten($a)->getData();
        $filtered = array_filter($data, function($val) { return !is_nan($val); });

        if (empty($filtered)) {
            return NAN;
        }

        return array_sum($filtered) / count($filtered);
    }
}