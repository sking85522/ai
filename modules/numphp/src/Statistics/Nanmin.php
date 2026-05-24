<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanmin
{
    /**
     * Return the minimum of an array or minimum along an axis, ignoring any NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function nanmin(NDArray $a, ?int $axis = null)
    {
        if ($axis !== null) {
            throw new \Exception("nanmin with axis not implemented yet.");
        }
        $data = Flatten::flatten($a)->getData();
        $filtered = array_filter($data, function($val) { return !is_nan($val); });

        return empty($filtered) ? NAN : min($filtered);
    }
}