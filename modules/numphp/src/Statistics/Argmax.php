<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Argmax
{
    /**
     * Returns the indices of the maximum values along an axis.
     * Currently supports flattened array (axis=null).
     */
    public static function argmax(NDArray $a): int
    {
        $flat = Flatten::flatten($a);
        $data = $flat->getData();

        if (!is_array($data) || empty($data)) {
            return 0;
        }

        // array_keys with max value gives keys, we take first one
        $maxVal = max($data);
        $keys = array_keys($data, $maxVal);

        return $keys[0];
    }
}