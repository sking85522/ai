<?php

namespace SciPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class Linalg
{
    /**
     * Solve a linear matrix equation, or system of linear scalar equations.
     * Computes the "exact" solution, x, of the well-determined, i.e., full rank, linear matrix equation ax = b.
     * This approximates scipy.linalg.solve.
     *
     * @param NDArray $a Coefficient matrix.
     * @param NDArray $b Ordinate or "dependent variable" values.
     * @return NDArray Solution to the system a x = b.
     */
    public static function solve(NDArray $a, NDArray $b): NDArray
    {
        $a_data = $a->getData();
        $b_data = $b->getData();

        $n = count($a_data);
        if ($n !== count($a_data[0]) || $n !== count($b_data)) {
            throw new \InvalidArgumentException("Matrix dimensions do not match for solving.");
        }

        // Gaussian elimination with partial pivoting
        for ($i = 0; $i < $n; $i++) {
            // Pivoting
            $max_el = abs($a_data[$i][$i]);
            $max_row = $i;
            for ($k = $i + 1; $k < $n; $k++) {
                if (abs($a_data[$k][$i]) > $max_el) {
                    $max_el = abs($a_data[$k][$i]);
                    $max_row = $k;
                }
            }

            // Swap rows
            $tmp = $a_data[$max_row];
            $a_data[$max_row] = $a_data[$i];
            $a_data[$i] = $tmp;

            $tmp = $b_data[$max_row];
            $b_data[$max_row] = $b_data[$i];
            $b_data[$i] = $tmp;

            if ($a_data[$i][$i] == 0) {
                throw new \RuntimeException("Matrix is singular.");
            }

            // Eliminate
            for ($k = $i + 1; $k < $n; $k++) {
                $c = -$a_data[$k][$i] / $a_data[$i][$i];
                for ($j = $i; $j < $n; $j++) {
                    if ($i == $j) {
                        $a_data[$k][$j] = 0;
                    } else {
                        $a_data[$k][$j] += $c * $a_data[$i][$j];
                    }
                }
                $b_data[$k] += $c * $b_data[$i];
            }
        }

        // Back substitution
        $x = array_fill(0, $n, 0);
        for ($i = $n - 1; $i >= 0; $i--) {
            $x[$i] = $b_data[$i] / $a_data[$i][$i];
            for ($k = $i - 1; $k >= 0; $k--) {
                $b_data[$k] -= $a_data[$k][$i] * $x[$i];
            }
        }

        return np::array($x);
    }
}
