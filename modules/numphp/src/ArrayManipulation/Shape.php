<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Shape
{
    public static function shape(NDArray $a): array
    {
        return $a->getShape();
    }
}
