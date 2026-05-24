<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Ndim
{
    public static function ndim(NDArray $a): int
    {
        return count($a->getShape());
    }
}
