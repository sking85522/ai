<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Tensorinv
{
    public static function tensorinv(NDArray $a): NDArray
    {
        return Inverse::inverse($a);
    }
}
