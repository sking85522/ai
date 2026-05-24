<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Copysign
{
    /**
     * Change the sign of x1 to that of x2, element-wise.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function copysign(NDArray $x1, NDArray $x2): NDArray
    {
        $d1 = $x1->getData();
        $d2 = $x2->getData();

        $recursive_op = function ($a, $b) use (&$recursive_op) {
            if (is_array($a)) {
                $b_arr = is_array($b) ? $b : array_fill(0, count($a), $b);
                return array_map($recursive_op, $a, $b_arr);
            }
            return ($b >= 0) ? abs($a) : -abs($a);
        };

        return new NDArray($recursive_op($d1, $d2), 'float');
    }
}