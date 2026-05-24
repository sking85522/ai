<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Eigvalsh
{
    public static function eigvalsh(NDArray $a): NDArray
    {
        return Eigvals::eigvals($a);
    }
}
