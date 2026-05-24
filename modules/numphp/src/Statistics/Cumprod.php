<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Cumprod
{
    /**
     * Return the cumulative product of elements along a given axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function cumprod(NDArray $a, ?int $axis = null): NDArray
    {
        // For now, only implementing for flattened array (axis=null)
        if ($axis !== null) {
            throw new \Exception("cumprod currently only supports axis=null.");
        }

        $flat = Flatten::flatten($a)->getData();
        if (!is_array($flat) || empty($flat)) {
            return new NDArray($flat, $a->getDtype());
        }

        $prod = 1;
        $result = [];
        foreach ($flat as $val) {
            $prod *= $val;
            $result[] = $prod;
        }
        return new NDArray($result, $a->getDtype());
    }
}