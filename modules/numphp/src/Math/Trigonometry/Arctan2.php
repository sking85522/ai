<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Arctan2
{
    /**
     * Element-wise arc tangent of x1/x2 choosing the quadrant correctly.
     *
     * @param NDArray $x1 y-coordinates
     * @param NDArray $x2 x-coordinates
     * @return NDArray
     */
    public static function arctan2(NDArray $x1, NDArray $x2): NDArray
    {
        $d1 = $x1->getData();
        $d2 = $x2->getData();
        $result = self::recursiveAtan2($d1, $d2);
        return new NDArray($result, 'float');
    }

    private static function recursiveAtan2($d1, $d2)
    {
        if (is_array($d1)) {
             $func = function ($a, $b) {
                return self::recursiveAtan2($a, $b);
            };
            $d2Arr = is_array($d2) ? $d2 : array_fill(0, count($d1), $d2);
            return array_map($func, $d1, $d2Arr);
        }
        return atan2($d1, $d2);
    }
}