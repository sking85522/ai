<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\Creation\Identity;

class MatrixPower
{
    /**
     * Raise a square matrix to the (integer) power n.
     *
     * @param NDArray $a
     * @param int $n
     * @return NDArray
     */
    public static function matrix_power(NDArray $a, int $n): NDArray
    {
        $shape = $a->getShape();
        if (count($shape) !== 2 || $shape[0] !== $shape[1]) {
            throw new \InvalidArgumentException("Input must be a square matrix.");
        }

        if ($n == 0) return Identity::identity($shape[0]);
        if ($n < 0) {
            $a = Inverse::inverse($a);
            $n = abs($n);
        }

        $res = $a;
        for ($i = 1; $i < $n; $i++) {
            $res = Matmul::matmul($res, $a);
        }

        return $res;
    }
}
