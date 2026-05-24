<?php

namespace NumPHP\Sets;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Intersect1D
{
    /**
     * Find the intersection of two arrays.
     *
     * @param NDArray $ar1
     * @param NDArray $ar2
     * @return NDArray
     */
    public static function intersect1d(NDArray $ar1, NDArray $ar2): NDArray
    {
        $data1 = Flatten::flatten($ar1)->getData();
        $data2 = Flatten::flatten($ar2)->getData();

        $intersection = array_intersect($data1, $data2);
        sort($intersection);

        return new NDArray(array_values($intersection));
    }
}
