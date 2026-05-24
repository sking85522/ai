<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Inv
{
    public static function inv(NDArray $a): NDArray
    {
        return Inverse::inverse($a);
    }
}
