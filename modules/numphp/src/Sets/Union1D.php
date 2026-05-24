<?php

namespace NumPHP\Sets;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Union1D
{
    /**
     * Find the union of two arrays.
     *
     * @param NDArray $ar1
     * @param NDArray $ar2
     * @return NDArray
     */
    public static function union1d(NDArray $ar1, NDArray $ar2): NDArray
    {
        $data1 = Flatten::flatten($ar1)->getData();
        $data2 = Flatten::flatten($ar2)->getData();

        $union = array_unique(array_merge($data1, $data2));
        sort($union);

        return new NDArray(array_values($union));
    }
}