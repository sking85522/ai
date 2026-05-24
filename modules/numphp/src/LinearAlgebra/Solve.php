<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\Core\DType;

class Solve
{
    /**
     * Solve a linear matrix equation, or system of linear scalar equations.
     * Computes the "exact" solution, x, of the well-determined, i.e., full rank, linear matrix equation ax = b.
     *
     * @param NDArray $a Coefficient matrix (N x N)
     * @param NDArray $b Ordinate or "dependent variable" values (N)
     * @return NDArray
     */
    public static function solve(NDArray $a, NDArray $b): NDArray
    {
        $shapeA = $a->getShape();
        $shapeB = $b->getShape();

        if (count($shapeA) !== 2 || $shapeA[0] !== $shapeA[1]) {
            throw new \InvalidArgumentException("Matrix a must be square");
        }
        if ($shapeA[0] !== $shapeB[0]) {
             throw new \InvalidArgumentException("Matrix a and vector b dimensions must match");
        }

        $n = $shapeA[0];
        $matA = $a->getData();
        // Handle b being 1D or 2D (column vector)
        $vecB = $b->getData();

        // Create augmented matrix
        for ($i = 0; $i < $n; $i++) {
            $matA[$i][] = is_array($vecB) ? (is_array($vecB[$i]) ? $vecB[$i][0] : $vecB[$i]) : $vecB;
        }

        // Gaussian Elimination
        for ($i = 0; $i < $n; $i++) {
            // Pivot
            $maxRow = $i;
            for ($k = $i + 1; $k < $n; $k++) {
                if (abs($matA[$k][$i]) > abs($matA[$maxRow][$i])) {
                    $maxRow = $k;
                }
            }

            // Swap
            $temp = $matA[$i];
            $matA[$i] = $matA[$maxRow];
            $matA[$maxRow] = $temp;

            if (abs($matA[$i][$i]) < 1e-12) {
                throw new \Exception("Singular matrix");
            }

            // Eliminate
            for ($k = $i + 1; $k < $n; $k++) {
                $factor = $matA[$k][$i] / $matA[$i][$i];
                for ($j = $i; $j <= $n; $j++) {
                    $matA[$k][$j] -= $factor * $matA[$i][$j];
                }
            }
        }

        // Back Substitution
        $x = array_fill(0, $n, 0.0);
        for ($i = $n - 1; $i >= 0; $i--) {
            $sum = 0;
            for ($j = $i + 1; $j < $n; $j++) {
                $sum += $matA[$i][$j] * $x[$j];
            }
            $x[$i] = ($matA[$i][$n] - $sum) / $matA[$i][$i];
        }

        // Round very small floats
        $x = array_map(function($val) {
             return (abs($val) < 1e-12) ? 0.0 : $val;
        }, $x);

        return new NDArray($x, DType::FLOAT);
    }
}