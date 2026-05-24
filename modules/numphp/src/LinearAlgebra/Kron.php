<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Kron
{
    /**
     * Kronecker product of two arrays.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function kron(NDArray $a, NDArray $b): NDArray
    {
        $dataA = $a->getData();
        $dataB = $b->getData();
        $shapeA = $a->getShape();
        $shapeB = $b->getShape();

        if (count($shapeA) !== 2 || count($shapeB) !== 2) {
            throw new \Exception("Kronecker product currently only supports 2D arrays.");
        }

        $new_rows = $shapeA[0] * $shapeB[0];
        $new_cols = $shapeA[1] * $shapeB[1];
        $result = array_fill(0, $new_rows, array_fill(0, $new_cols, 0));

        for ($i = 0; $i < $shapeA[0]; $i++) {
            for ($j = 0; $j < $shapeA[1]; $j++) {
                for ($k = 0; $k < $shapeB[0]; $k++) {
                    for ($l = 0; $l < $shapeB[1]; $l++) {
                        $result[$i * $shapeB[0] + $k][$j * $shapeB[1] + $l] = $dataA[$i][$j] * $dataB[$k][$l];
                    }
                }
            }
        }
        return new NDArray($result);
    }
}