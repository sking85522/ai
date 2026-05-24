<?php

namespace NumPHP\Math\Comparison;

use NumPHP\Core\NDArray;

class ArrayEqual
{
    public static function array_equal(NDArray $a, NDArray $b): bool
    {
        return $a->getShape() === $b->getShape() && $a->getData() == $b->getData();
    }
}
