<?php

namespace NumPHP\Math\Logical;

use NumPHP\Core\NDArray;

class LogicalAnd
{
    /**
     * Compute the truth value of a AND b element-wise.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function logical_and(NDArray $a, NDArray $b): NDArray
    {
        $d1 = $a->getData();
        $d2 = $b->getData();
        $rec = function($item1, $item2) use (&$rec) {
            if (is_array($item1)) return array_map($rec, $item1, $item2);
            return $item1 && $item2;
        };
        return new NDArray($rec($d1, $d2), 'bool');
    }
}