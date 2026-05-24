<?php

namespace NumPHP\Math\Comparison;

use NumPHP\Core\NDArray;

class ArrayEquiv
{
    public static function array_equiv(NDArray $a, NDArray $b): bool
    {
        return $a->getShape() === $b->getShape() && $a->getData() == $b->getData();
    }
}
