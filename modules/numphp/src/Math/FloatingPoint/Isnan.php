<?php

namespace NumPHP\Math\FloatingPoint;

use NumPHP\Core\NDArray;

class Isnan
{
    /**
     * Test element-wise for NaN and return result as a boolean array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function isnan(NDArray $a): NDArray
    {
        $data = $a->getData();

        $rec = function($item) use (&$rec) {
            if (is_array($item)) {
                return array_map($rec, $item);
            }
            return is_nan($item);
        };

        return new NDArray($rec($data), 'bool');
    }
}
