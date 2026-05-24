<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Sqrt
{
    public static function sqrt(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveSqrt($data);
        return new NDArray($result, $a->getDtype());
    }

    private static function recursiveSqrt($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveSqrt'], $data);
        }
        if ($data < 0) {
            return NAN; // NumPy returns NaN for negative sqrt without complex type
        }
        return sqrt($data);
    }
}