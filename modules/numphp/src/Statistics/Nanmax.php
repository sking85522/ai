<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanmax
{
    /**
     * Return the maximum of an array or maximum along an axis, ignoring any NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function nanmax(NDArray $a, ?int $axis = null)
    {
        if ($axis !== null) {
            throw new \Exception("nanmax with axis not implemented yet.");
        }
        $data = Flatten::flatten($a)->getData();
        $filtered = array_filter($data, function($val) { return !is_nan($val); });

        return empty($filtered) ? NAN : max($filtered);
    }
}