<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Multiply
{
    public static function multiply(NDArray $a, NDArray $b): NDArray
    {
        $dataA = $a->getData();
        $dataB = $b->getData();
        $resultData = self::recursiveMultiply($dataA, $dataB);
        return new NDArray($resultData);
    }

    private static function recursiveMultiply($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            $result = [];
            for ($i = 0; $i < count($a); $i++) {
                $result[] = self::recursiveMultiply($a[$i], $b[$i]);
            }
            return $result;
        }
        return $a * $b;
    }
}