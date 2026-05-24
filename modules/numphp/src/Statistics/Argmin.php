<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Argmin
{
    /**
     * Returns the indices of the minimum values along an axis.
     * Currently supports flattened array (axis=null).
     */
    public static function argmin(NDArray $a): int
    {
        $flat = Flatten::flatten($a);
        $data = $flat->getData();

        if (!is_array($data) || empty($data)) {
            return 0;
        }

        $minVal = min($data);
        $keys = array_keys($data, $minVal);

        return $keys[0];
    }
}