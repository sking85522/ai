<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Percentile
{
    /**
     * Compute the q-th percentile of the data along the specified axis.
     *
     * @param NDArray $a
     * @param float $q Percentile or sequence of percentiles to compute, which must be between 0 and 100 inclusive.
     * @return float
     */
    public static function percentile(NDArray $a, float $q): float
    {
        $data = Flatten::flatten($a)->getData();
        sort($data);
        $count = count($data);
        if ($count == 0) return 0.0;

        $index = ($q / 100) * ($count - 1);
        $lower = floor($index);
        $upper = ceil($index);
        $fraction = $index - $lower;

        if ($lower == $upper) {
            return $data[(int)$index];
        }

        // Linear interpolation
        return $data[(int)$lower] + ($data[(int)$upper] - $data[(int)$lower]) * $fraction;
    }
}