<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Atleast1d;
use NumPHP\ArrayManipulation\Concatenate;

class Hstack
{
    /**
     * Stack arrays in sequence horizontally (column wise).
     *
     * @param array $tup Sequence of NDArrays.
     * @return NDArray
     */
    public static function hstack(array $tup): NDArray
    {
        $arrays = array_map([Atleast1d::class, 'atleast_1d'], $tup);

        // For 1D arrays, stack as columns (axis 1)
        // For 2D arrays, stack as columns (axis 1)
        if (count($arrays[0]->getShape()) === 1) {
            return Concatenate::concatenate($arrays, 0);
        }

        return Concatenate::concatenate($arrays, 1);
    }
}
