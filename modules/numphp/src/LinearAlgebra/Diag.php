<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Diag
{
    /**
     * Extract a diagonal or construct a diagonal array.
     *
     * @param NDArray $v If v is a 2-D array, return a copy of its k-th diagonal.
     *                   If v is a 1-D array, return a 2-D array with v on the k-th diagonal.
     * @param int $k Diagonal in question. The default is 0.
     * @return NDArray
     */
    public static function diag(NDArray $v, int $k = 0): NDArray
    {
        $shape = $v->getShape();
        $data = $v->getData();

        if ($k !== 0) {
             throw new \Exception("Diag currently only supports k=0");
        }

        // Case 1: 1-D Array -> Construct 2-D diagonal matrix
        if (count($shape) === 1) {
            $n = $shape[0];
            // Initialize NxN with 0
            $res = array_fill(0, $n, array_fill(0, $n, 0));
            for ($i = 0; $i < $n; $i++) {
                $res[$i][$i] = $data[$i];
            }
            return new NDArray($res, $v->getDtype());
        }

        // Case 2: 2-D Array -> Extract diagonal
        if (count($shape) === 2) {
            $limit = min($shape[0], $shape[1]);
            $diag = [];
            for ($i = 0; $i < $limit; $i++) {
                $diag[] = $data[$i][$i];
            }
            return new NDArray($diag, $v->getDtype());
        }

        throw new \InvalidArgumentException("Input must be 1-d or 2-d");
    }
}