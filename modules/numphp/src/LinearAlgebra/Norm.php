<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Norm
{
    /**
     * Matrix or vector norm.
     * Currently implements Frobenius norm for matrices and 2-norm for vectors.
     *
     * @param NDArray $x
     * @return float
     */
    public static function norm(NDArray $x): float
    {
        $data = $x->getData();
        $shape = $x->getShape();
        $ndim = count($shape);

        // Flatten and compute sum of squares
        $flat = Flatten::flatten($x)->getData();
        if (!is_array($flat)) $flat = [$flat];

        $sumSq = 0.0;
        foreach ($flat as $val) {
            $sumSq += $val * $val;
        }

        return sqrt($sumSq);
    }
}