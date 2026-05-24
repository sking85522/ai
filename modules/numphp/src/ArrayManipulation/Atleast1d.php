<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Atleast1d
{
    /**
     * Convert inputs to arrays with at least one dimension.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function atleast_1d(NDArray $a): NDArray
    {
        if (count($a->getShape()) >= 1) {
            return $a;
        }
        return new NDArray([$a->getData()]);
    }
}