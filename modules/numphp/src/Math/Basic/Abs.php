<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Abs
{
    public static function abs(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveAbs($data);
        return new NDArray($result, $a->getDType());
    }

    private static function recursiveAbs($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveAbs'], $data);
        }
        return abs($data);
    }
}
