<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Minimum
{
    /**
     * Element-wise minimum of array elements.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function minimum(NDArray $x1, NDArray $x2): NDArray
    {
        $d1 = $x1->getData();
        $d2 = $x2->getData();
        $rec = function($a, $b) use (&$rec) {
             if (is_array($a)) {
                 return array_map($rec, $a, is_array($b) ? $b : array_fill(0, count($a), $b));
             }
             return min($a, $b);
        };
        return new NDArray($rec($d1, $d2), 'float');
    }
}
