<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\Creation\Tri as CreationTri;

class Tri
{
    public static function tri(int $N, ?int $M = null, int $k = 0, ?string $dtype = null): NDArray
    {
        return CreationTri::tri($N, $M, $k, $dtype);
    }
}
