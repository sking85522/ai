<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Eigh
{
    public static function eigh(NDArray $a): array
    {
        return Eig::eig($a);
    }
}
