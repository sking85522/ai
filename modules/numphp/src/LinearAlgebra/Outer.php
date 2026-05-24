<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Outer
{
    /**
     * Compute the outer product of two vectors.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function outer(NDArray $a, NDArray $b): NDArray
    {
        $d1 = Flatten::flatten($a)->getData();
        $d2 = Flatten::flatten($b)->getData();

        $result = [];
        foreach ($d1 as $val1) {
            $row = [];
            foreach ($d2 as $val2) {
                $row[] = $val1 * $val2;
            }
            $result[] = $row;
        }
        return new NDArray($result);
    }
}
