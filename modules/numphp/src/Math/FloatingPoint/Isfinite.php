<?php

namespace NumPHP\Math\FloatingPoint;

use NumPHP\Core\NDArray;

class Isfinite
{
    /**
     * Test element-wise for finiteness (not infinity or not Not a Number).
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function isfinite(NDArray $a): NDArray
    {
        $data = $a->getData();

        $rec = function($item) use (&$rec) {
            if (is_array($item)) {
                return array_map($rec, $item);
            }
            return is_finite($item);
        };

        return new NDArray($rec($data), 'bool');
    }
}
