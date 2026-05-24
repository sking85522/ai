<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Rollaxis
{
    public static function rollaxis(NDArray $a, int $axis, int $start = 0): NDArray
    {
        return Swapaxes::swapaxes($a, $axis, $start);
    }
}
