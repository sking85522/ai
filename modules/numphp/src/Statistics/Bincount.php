<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Bincount
{
    /**
     * Count number of occurrences of each value in an array of non-negative ints.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function bincount(NDArray $a): NDArray
    {
        $data = Flatten::flatten($a)->getData();
        if (empty($data)) {
            return new NDArray([], 'int');
        }
        $max = max($data);
        $bins = array_fill(0, $max + 1, 0);
        foreach ($data as $val) {
            if ($val >= 0) {
                $bins[(int)$val]++;
            }
        }
        return new NDArray($bins, 'int');
    }
}