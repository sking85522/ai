<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Einsum
{
    public static function einsum(string $subscripts, NDArray $a, NDArray $b = null)
    {
        if ($subscripts === 'i,i->' && $b !== null) {
            return Dot::dot($a, $b);
        }
        if ($subscripts === 'ij,jk->ik' && $b !== null) {
            return Matmul::matmul($a, $b);
        }
        throw new \Exception('einsum pattern not supported in this implementation');
    }
}
