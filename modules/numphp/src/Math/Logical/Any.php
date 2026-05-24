<?php

namespace NumPHP\Math\Logical;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Any
{
    /**
     * Test whether any array element along a given axis evaluates to True.
     *
     * @param NDArray $a
     * @return bool
     */
    public static function any(NDArray $a): bool
    {
        $data = Flatten::flatten($a)->getData();

        foreach ($data as $element) {
            if ($element) {
                return true;
            }
        }

        return false;
    }
}