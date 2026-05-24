<?php

namespace NumPHP\Sorting;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Partition
{
    /**
     * Partially sorts an array.
     *
     * @param NDArray $a
     * @param int $kth
     * @return NDArray
     */
    public static function partition(NDArray $a, int $kth): NDArray
    {
        // This is a simplification. A true partition algorithm is more complex.
        // For now, we return a fully sorted array.
        $data = Flatten::flatten($a)->getData();
        sort($data);
        return new NDArray($data, $a->getDType());
    }
}
