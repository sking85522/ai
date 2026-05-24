<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Cumsum
{
    /**
     * Return the cumulative sum of the elements along a given axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function cumsum(NDArray $a, ?int $axis = null): NDArray
    {
        // For now, only implementing for flattened array (axis=null)
        if ($axis !== null) {
            throw new \Exception("cumsum currently only supports axis=null.");
        }

        $flat = Flatten::flatten($a)->getData();
        if (!is_array($flat) || empty($flat)) {
            return new NDArray($flat, $a->getDtype());
        }

        $sum = 0;
        $result = [];
        foreach ($flat as $val) {
            $sum += $val;
            $result[] = $sum;
        }
        return new NDArray($result, $a->getDtype());
    }
}