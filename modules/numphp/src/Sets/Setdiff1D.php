<?php

namespace NumPHP\Sets;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Setdiff1D
{
    /**
     * Find the set difference of two arrays.
     *
     * @param NDArray $ar1
     * @param NDArray $ar2
     * @return NDArray
     */
    public static function setdiff1d(NDArray $ar1, NDArray $ar2): NDArray
    {
        $data1 = Flatten::flatten($ar1)->getData();
        $data2 = Flatten::flatten($ar2)->getData();

        $difference = array_diff($data1, $data2);

        return new NDArray(array_values($difference));
    }
}