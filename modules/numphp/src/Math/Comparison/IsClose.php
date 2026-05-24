<?php

namespace NumPHP\Math\Comparison;

use NumPHP\Core\NDArray;

class IsClose
{
    /**
     * Returns a boolean array where two arrays are element-wise equal within a tolerance.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @param float $rtol Relative tolerance.
     * @param float $atol Absolute tolerance.
     * @param bool $equal_nan
     * @return NDArray
     */
    public static function isclose(NDArray $a, NDArray $b, float $rtol = 1e-05, float $atol = 1e-08, bool $equal_nan = false): NDArray
    {
        $dataA = $a->getData();
        $dataB = $b->getData();

        $rec = function($itemA, $itemB) use ($rtol, $atol, $equal_nan, &$rec) {
            if (is_array($itemA)) {
                 $itemB_arr = is_array($itemB) ? $itemB : array_fill(0, count($itemA), $itemB);
                 return array_map($rec, $itemA, $itemB_arr);
            }

            if (is_nan($itemA) && is_nan($itemB)) return $equal_nan;
            if (is_infinite($itemA) && is_infinite($itemB)) return ($itemA > 0) === ($itemB > 0);
            return abs($itemA - $itemB) <= ($atol + $rtol * abs($itemB));
        };

        return new NDArray($rec($dataA, $dataB), 'bool');
    }
}