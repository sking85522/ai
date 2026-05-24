<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Size
{
    public static function size(NDArray $a): int
    {
        return $a->getSize();
    }
}
