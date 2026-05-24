<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Determinant
{
    public static function det(NDArray $a): float
    {
        $shape = $a->getShape();
        if (count($shape) !== 2 || $shape[0] !== $shape[1]) {
            throw new \InvalidArgumentException("Determinant needs a square 2D matrix");
        }

        $n = $shape[0];
        $matrix = $a->getData();
        $det = 1.0;

        // Gaussian Elimination to Upper Triangular Form
        for ($i = 0; $i < $n; $i++) {
            // Find pivot
            $pivot = $i;
            while ($pivot < $n && $matrix[$pivot][$i] == 0) {
                $pivot++;
            }

            if ($pivot == $n) {
                return 0.0; // Singular matrix
            }

            // Swap rows
            if ($pivot !== $i) {
                $temp = $matrix[$i];
                $matrix[$i] = $matrix[$pivot];
                $matrix[$pivot] = $temp;
                $det *= -1; // Swap changes sign
            }

            $det *= $matrix[$i][$i];

            // Eliminate rows below
            for ($j = $i + 1; $j < $n; $j++) {
                if ($matrix[$j][$i] != 0) {
                    $factor = $matrix[$j][$i] / $matrix[$i][$i];
                    for ($k = $i; $k < $n; $k++) {
                        $matrix[$j][$k] -= $factor * $matrix[$i][$k];
                    }
                }
            }
        }

        // Rounding to avoid floating point errors for integers
        if (abs($det - round($det)) < 1e-9) {
            return (float)round($det);
        }
        return $det;
    }
}