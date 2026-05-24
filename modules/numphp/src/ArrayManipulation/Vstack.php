<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Atleast2d;
use NumPHP\ArrayManipulation\Concatenate;

class Vstack
{
    /**
     * Stack arrays in sequence vertically (row wise).
     *
     * This is equivalent to `concatenate` along axis 0 after reshaping 1-D arrays to 2-D arrays.
     *
     * @param array $tup Sequence of NDArrays.
     * @return NDArray
     */
    public static function vstack(array $tup): NDArray
    {
        $arrays = array_map([Atleast2d::class, 'atleast_2d'], $tup);
        return Concatenate::concatenate($arrays, 0);
    }
}
