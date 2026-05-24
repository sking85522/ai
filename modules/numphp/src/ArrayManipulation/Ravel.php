<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Ravel
{
    /**
     * Return a contiguous flattened array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function ravel(NDArray $a): NDArray
    {
        return Flatten::flatten($a);
    }
}