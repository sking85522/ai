<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanmedian
{
    /**
     * Compute the median along the specified axis, while ignoring NaNs.
     *
     * @param NDArray $a
     * @return float
     */
    public static function nanmedian(NDArray $a): float
    {
        $data = Flatten::flatten($a)->getData();
        $filtered = array_values(array_filter($data, function($val) { return !is_nan($val); }));

        if (empty($filtered)) {
            return NAN;
        }

        sort($filtered);
        $count = count($filtered);
        $middle = floor(($count - 1) / 2);

        return ($count % 2) ? $filtered[$middle] : (($filtered[$middle] + $filtered[$middle + 1]) / 2.0);
    }
}