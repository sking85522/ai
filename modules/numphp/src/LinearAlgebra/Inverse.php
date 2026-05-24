<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\Core\DType;

class Inverse
{
    public static function inverse(NDArray $a): NDArray
    {
        $shape = $a->getShape();
        if (count($shape) !== 2 || $shape[0] !== $shape[1]) {
            throw new \InvalidArgumentException("Inverse needs a square 2D matrix");
        }

        $n = $shape[0];
        $m = $a->getData();
        $I = [];

        // Create augmented matrix [M | I]
        for ($i = 0; $i < $n; $i++) {
            $I[$i] = array_fill(0, $n, 0.0);
            $I[$i][$i] = 1.0;
        }

        // Forward Elimination
        for ($i = 0; $i < $n; $i++) {
            $pivot = $m[$i][$i];

            if ($pivot == 0) {
                // Look for a non-zero pivot in lower rows
                for ($k = $i + 1; $k < $n; $k++) {
                    if ($m[$k][$i] != 0) {
                        // Swap rows in M
                        $temp = $m[$i];
                        $m[$i] = $m[$k];
                        $m[$k] = $temp;
                        // Swap rows in I
                        $temp = $I[$i];
                        $I[$i] = $I[$k];
                        $I[$k] = $temp;
                        $pivot = $m[$i][$i];
                        break;
                    }
                }
                if ($pivot == 0) {
                    throw new \Exception("Singular matrix cannot be inverted");
                }
            }

            // Scale row to make pivot 1
            for ($j = 0; $j < $n; $j++) {
                $m[$i][$j] /= $pivot;
                $I[$i][$j] /= $pivot;
            }

            // Eliminate other rows
            for ($k = 0; $k < $n; $k++) {
                if ($k !== $i) {
                    $factor = $m[$k][$i];
                    for ($j = 0; $j < $n; $j++) {
                        $m[$k][$j] -= $factor * $m[$i][$j];
                        $I[$k][$j] -= $factor * $I[$i][$j];
                    }
                }
            }
        }

        return new NDArray($I, DType::FLOAT);
    }
}