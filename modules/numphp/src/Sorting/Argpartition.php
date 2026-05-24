<?php

namespace NumPHP\Sorting;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Argpartition
{
    /**
     * Indirect partial sort.
     *
     * @param NDArray $a
     * @param int $kth
     * @return NDArray
     */
    public static function argpartition(NDArray $a, int $kth): NDArray
    {
        // This is a simplification. A true argpartition is more complex.
        // For now, we return the indices of a fully sorted array.
        $data = Flatten::flatten($a)->getData();
        asort($data);
        return new NDArray(array_keys($data), 'int');
    }
}