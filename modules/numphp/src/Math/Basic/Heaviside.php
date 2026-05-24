<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Heaviside
{
    /**
     * Compute the Heaviside step function.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function heaviside(NDArray $x1, NDArray $x2): NDArray
    {
        $d1 = $x1->getData();
        $d2 = $x2->getData();
        $rec = function($a, $b) use (&$rec) {
             if (is_array($a)) return array_map($rec, $a, is_array($b) ? $b : array_fill(0, count($a), $b));
             if ($a == 0) return $b;
             return ($a > 0) ? 1.0 : 0.0;
        };
        return new NDArray($rec($d1, $d2), 'float');
    }
}