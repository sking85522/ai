<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Fmod
{
    /**
     * Return the element-wise remainder of division.
     *
     * @param NDArray $x1
     * @param mixed $x2 NDArray or scalar
     * @return NDArray
     */
    public static function fmod(NDArray $x1, $x2): NDArray
    {
        $d1 = $x1->getData();
        $d2 = ($x2 instanceof NDArray) ? $x2->getData() : $x2;
        $result = self::recursiveFmod($d1, $d2);
        return new NDArray($result, 'float');
    }

    private static function recursiveFmod($d1, $d2)
    {
        if (is_array($d1)) {
            $d2Arr = is_array($d2) ? $d2 : array_fill(0, count($d1), $d2);
            return array_map([self::class, 'recursiveFmod'], $d1, $d2Arr);
        }
        return fmod($d1, $d2);
    }
}