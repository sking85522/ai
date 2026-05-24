<?php

namespace SciPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class AdvancedLinalg
{
    /**
     * Compute the determinant of an array.
     * Approximates scipy.linalg.det
     *
     * @param NDArray $a A 2D square matrix.
     * @return float Determinant of $a.
     */
    public static function det(NDArray $a): float
    {
        $mat = $a->getData();
        $n = count($mat);

        if ($n !== count($mat[0])) {
            throw new \InvalidArgumentException("Matrix must be square.");
        }

        if ($n === 1) return $mat[0][0];
        if ($n === 2) return ($mat[0][0] * $mat[1][1]) - ($mat[0][1] * $mat[1][0]);

        $det = 0;
        $sign = 1;

        for ($i = 0; $i < $n; $i++) {
            $subMat = [];
            for ($j = 1; $j < $n; $j++) {
                $row = [];
                for ($k = 0; $k < $n; $k++) {
                    if ($k !== $i) {
                        $row[] = $mat[$j][$k];
                    }
                }
                $subMat[] = $row;
            }
            $det += $sign * $mat[0][$i] * self::det(np::array($subMat));
            $sign = -$sign;
        }

        return $det;
    }

    /**
     * Compute the (multiplicative) inverse of a matrix.
     * Approximates scipy.linalg.inv
     *
     * @param NDArray $a A 2D square matrix.
     * @return NDArray Matrix inverse of $a.
     */
    public static function inv(NDArray $a): NDArray
    {
        $mat = $a->getData();
        $n = count($mat);

        if ($n !== count($mat[0])) {
            throw new \InvalidArgumentException("Matrix must be square.");
        }

        // Create augmented matrix [A | I]
        $aug = [];
        for ($i = 0; $i < $n; $i++) {
            $row = $mat[$i];
            for ($j = 0; $j < $n; $j++) {
                $row[] = ($i === $j) ? 1.0 : 0.0;
            }
            $aug[] = $row;
        }

        // Gauss-Jordan elimination
        for ($i = 0; $i < $n; $i++) {
            // Find pivot
            if ($aug[$i][$i] == 0) {
                $found = false;
                for ($j = $i + 1; $j < $n; $j++) {
                    if ($aug[$j][$i] != 0) {
                        $tmp = $aug[$i];
                        $aug[$i] = $aug[$j];
                        $aug[$j] = $tmp;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    throw new \RuntimeException("Singular matrix.");
                }
            }

            $pivot = $aug[$i][$i];
            for ($j = 0; $j < 2 * $n; $j++) {
                $aug[$i][$j] /= $pivot;
            }

            for ($j = 0; $j < $n; $j++) {
                if ($i !== $j) {
                    $factor = $aug[$j][$i];
                    for ($k = 0; $k < 2 * $n; $k++) {
                        $aug[$j][$k] -= $factor * $aug[$i][$k];
                    }
                }
            }
        }

        // Extract inverse matrix
        $inv = [];
        for ($i = 0; $i < $n; $i++) {
            $inv[] = array_slice($aug[$i], $n);
        }

        return np::array($inv);
    }
}
