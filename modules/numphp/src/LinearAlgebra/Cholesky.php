<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Cholesky
{
    public static function cholesky(NDArray $a): NDArray
    {
        // Basic Cholesky decomposition L * L^T = A
        $matrix = $a->getData();
        $n = count($matrix);
        $L = array_fill(0, $n, array_fill(0, $n, 0.0));

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j <= $i; $j++) {
                $sum = 0;
                if ($j == $i) {
                    for ($k = 0; $k < $j; $k++) {
                        $sum += pow($L[$j][$k], 2);
                    }
                    $L[$j][$j] = sqrt($matrix[$j][$j] - $sum);
                } else {
                    for ($k = 0; $k < $j; $k++) {
                        $sum += ($L[$i][$k] * $L[$j][$k]);
                    }
                    if ($L[$j][$j] != 0) {
                        $L[$i][$j] = ($matrix[$i][$j] - $sum) / $L[$j][$j];
                    }
                }
            }
        }

        return new NDArray($L, $a->getDtype());
    }
}