<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Transpose;

class MatrixTranspose
{
    public static function matrix_transpose(NDArray $a): NDArray
    {
        return Transpose::transpose($a);
    }
}
