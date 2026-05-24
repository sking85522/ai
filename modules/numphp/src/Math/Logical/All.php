<?php

namespace NumPHP\Math\Logical;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class All
{
    /**
     * Test whether all array elements along a given axis evaluate to True.
     *
     * @param NDArray $a
     * @return bool
     */
    public static function all(NDArray $a): bool
    {
        $data = Flatten::flatten($a)->getData();

        foreach ($data as $element) {
            if (!$element) {
                return false;
            }
        }

        return true;
    }
}