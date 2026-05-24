<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Inner
{
    /**
     * Inner product of two arrays.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return mixed
     */
    public static function inner(NDArray $a, NDArray $b)
    {
        // For 1D arrays, inner product is dot product
        if (count($a->getShape()) === 1 && count($b->getShape()) === 1) {
             return Dot::dot($a, $b);
        }
        // For higher dimensions, sum product over the last axes
        // Simplified: using Tensordot usually, here simplified to Dot for now
        return Dot::dot($a, \NumPHP\ArrayManipulation\Transpose::transpose($b));
    }
}
