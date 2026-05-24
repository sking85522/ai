<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Atleast2d
{
    /**
     * View inputs as arrays with at least two dimensions.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function atleast_2d(NDArray $a): NDArray
    {
        $ndim = count($a->getShape());
        if ($ndim >= 2) {
            return $a;
        }
        if ($ndim === 0) { // scalar
            return new NDArray([[$a->getData()]]);
        }
        // 1D array
        return new NDArray([$a->getData()]);
    }
}