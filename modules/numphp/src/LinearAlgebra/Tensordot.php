<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Tensordot
{
    public static function tensordot(NDArray $a, NDArray $b, int $axes = 1)
    {
        if ($axes === 1) {
            return Matmul::matmul($a, $b);
        }
        throw new \Exception('tensordot only supports axes=1 in this implementation');
    }
}
