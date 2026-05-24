<?php

namespace NumPHP\Math\FloatingPoint;

use NumPHP\Core\NDArray;

class Isinf
{
    /**
     * Test element-wise for positive or negative infinity.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function isinf(NDArray $a): NDArray
    {
        $data = $a->getData();

        $rec = function($item) use (&$rec) {
            if (is_array($item)) {
                return array_map($rec, $item);
            }
            return is_infinite($item);
        };

        return new NDArray($rec($data), 'bool');
    }
}
