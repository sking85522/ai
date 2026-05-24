<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Transpose;

class Pinv
{
    /**
     * Compute the (Moore-Penrose) pseudo-inverse of a matrix.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function pinv(NDArray $a): NDArray
    {
        // Using Normal Equation: (A.T * A)^-1 * A.T
        // This is not robust for singular matrices, where SVD is preferred.
        $aT = Transpose::transpose($a);
        $aTa = Matmul::matmul($aT, $a);
        $aTa_inv = Inverse::inverse($aTa);
        return Matmul::matmul($aTa_inv, $aT);
    }
}