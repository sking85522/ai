<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Add
{
    public static function add(NDArray $a, NDArray $b): NDArray
    {
        $dataA = $a->getData();
        $dataB = $b->getData();
        $resultData = self::recursiveAdd($dataA, $dataB);
        return new NDArray($resultData);
    }

    private static function recursiveAdd($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            $result = [];
            for ($i = 0; $i < count($a); $i++) {
                $result[] = self::recursiveAdd($a[$i], $b[$i]);
            }
            return $result;
        }
        return $a + $b;
    }
}