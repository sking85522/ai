<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Divide
{
    public static function divide(NDArray $a, NDArray $b): NDArray
    {
        $dataA = $a->getData();
        $dataB = $b->getData();
        $resultData = self::recursiveDivide($dataA, $dataB);
        return new NDArray($resultData);
    }

    private static function recursiveDivide($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            $result = [];
            for ($i = 0; $i < count($a); $i++) {
                $result[] = self::recursiveDivide($a[$i], $b[$i]);
            }
            return $result;
        }
        return $a / $b;
    }
}