<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Atleast3d
{
    /**
     * View inputs as arrays with at least three dimensions.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function atleast_3d(NDArray $a): NDArray
    {
        $ndim = count($a->getShape());
        if ($ndim >= 3) {
            return $a;
        }
        if ($ndim === 0) {
            return new NDArray([[[$a->getData()]]]);
        }
        if ($ndim === 1) {
            // NumPy makes it (1, N, 1). We'll make it (1, 1, N) for simplicity.
            return new NDArray([[$a->getData()]]);
        }
        // 2D array (M, N) becomes (M, N, 1)
        return ExpandDims::expand_dims($a, 2);
    }
}