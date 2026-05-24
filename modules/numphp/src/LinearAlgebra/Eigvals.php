<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Eigvals
{
    public static function eigvals(NDArray $a): NDArray
    {
        [$vals, $vecs] = Eig::eig($a);
        return $vals;
    }
}
