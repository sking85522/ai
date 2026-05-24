<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nansum
{
    /**
     * Return the sum of array elements over a given axis, treating NaNs as zero.
     *
     * @param NDArray $a
     * @return mixed
     */
    public static function nansum(NDArray $a)
    {
        $data = Flatten::flatten($a)->getData();
        $filtered = array_map(function($val) { return is_nan($val) ? 0 : $val; }, $data);

        return array_sum($filtered);
    }
}