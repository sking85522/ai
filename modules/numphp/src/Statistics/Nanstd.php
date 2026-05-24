<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Nanstd
{
    /**
     * Compute the standard deviation along the specified axis, while ignoring NaNs.
     *
     * @param NDArray $a
     * @return float
     */
    public static function nanstd(NDArray $a): float
    {
        $var = Nanvar::nanvar($a);
        return is_nan($var) ? NAN : sqrt($var);
    }
}