<?php

namespace NumPHP\Math\Comparison;

use NumPHP\Core\NDArray;

class GreaterEqual
{
    /**
     * Return the truth value of (a >= b) element-wise.
     *
     * @param NDArray $a
     * @param mixed $b Can be NDArray or scalar
     * @return NDArray
     */
    public static function greater_equal(NDArray $a, $b): NDArray
    {
        $dataA = $a->getData();
        $dataB = ($b instanceof NDArray) ? $b->getData() : $b;

        $rec = function($itemA, $itemB) use (&$rec) {
            if (is_array($itemA)) {
                $itemB_arr = is_array($itemB) ? $itemB : array_fill(0, count($itemA), $itemB);
                return array_map($rec, $itemA, $itemB_arr);
            }
            return $itemA >= $itemB;
        };

        return new NDArray($rec($dataA, $dataB), 'bool');
    }
}
