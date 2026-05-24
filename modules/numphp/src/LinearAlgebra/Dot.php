<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\Math\Basic\Multiply;
use NumPHP\Statistics\Sum;

class Dot
{
    /**
     * Dot product of two arrays.
     * - If both are 1-D arrays, it is inner product of vectors.
     * - If both are 2-D arrays, it is matrix multiplication.
     * - If one is scalar, it is multiplication.
     */
    public static function dot($a, $b)
    {
        // Handle Scalar cases
        if (!($a instanceof NDArray) || !($b instanceof NDArray)) {
             // If one is NDArray and other is scalar, use Multiply
             if ($a instanceof NDArray) {
                 return Multiply::multiply($a, new NDArray($b));
             }
             if ($b instanceof NDArray) {
                 return Multiply::multiply(new NDArray($a), $b);
             }
             return $a * $b;
        }

        $shapeA = $a->getShape();
        $shapeB = $b->getShape();

        // Case 1: Both 1-D (Vector dot product) -> Returns Scalar (float)
        if (count($shapeA) === 1 && count($shapeB) === 1) {
            if ($shapeA[0] !== $shapeB[0]) {
                throw new \InvalidArgumentException("Shapes must match for 1D dot product");
            }
            // Element-wise multiply then sum
            $prod = Multiply::multiply($a, $b);
            return Sum::sum($prod);
        }

        // Case 2: Both 2-D (Matrix Multiplication)
        if (count($shapeA) === 2 && count($shapeB) === 2) {
            return Matmul::matmul($a, $b);
        }

        throw new \Exception("Dot product currently only implemented for 1D vectors and 2D matrices.");
    }
}