<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Tensorsolve
{
    public static function tensorsolve(NDArray $a, NDArray $b): NDArray
    {
        return Solve::solve($a, $b);
    }
}
