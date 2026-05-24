<?php

namespace NumPHP\Math\Logical;

use NumPHP\Core\NDArray;

class LogicalNot
{
    /**
     * Compute the truth value of NOT x element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function logical_not(NDArray $a): NDArray
    {
        $data = $a->getData();
        $rec = function($item) use (&$rec) {
            if (is_array($item)) return array_map($rec, $item);
            return !$item;
        };
        return new NDArray($rec($data), 'bool');
    }
}