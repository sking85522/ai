<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Transpose;

class Lstsq
{
    /**
     * Return the least-squares solution to a linear matrix equation.
     * Solves the equation a x = b by computing a vector x that minimizes the squared Euclidean 2-norm || b - a x ||^2.
     * Uses the Normal Equation: x = (A.T * A)^-1 * A.T * b
     *
     * @param NDArray $a Coefficient matrix
     * @param NDArray $b Dependent variable values
     * @return NDArray
     */
    public static function lstsq(NDArray $a, NDArray $b): NDArray
    {
        // Calculate A Transpose
        $aT = Transpose::transpose($a);

        // Calculate A.T * A
        $aTa = Matmul::matmul($aT, $a);

        // Calculate Inverse of (A.T * A)
        // Note: If singular, this will throw. SVD based approach is more robust but complex for pure PHP.
        $inv = Inverse::inverse($aTa);

        // Calculate A.T * b
        $aTb = Matmul::matmul($aT, $b);

        // Final result x = inv * (A.T * b)
        return Matmul::matmul($inv, $aTb);
    }
}