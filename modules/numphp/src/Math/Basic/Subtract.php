<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Subtract
{
    public static function subtract(NDArray $a, NDArray $b): NDArray
    {
        $dataA = $a->getData();
        $dataB = $b->getData();
        $resultData = self::recursiveSubtract($dataA, $dataB);
        return new NDArray($resultData);
    }

    private static function recursiveSubtract($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            $result = [];
            for ($i = 0; $i < count($a); $i++) {
                $result[] = self::recursiveSubtract($a[$i], $b[$i]);
            }
            return $result;
        }
        return $a - $b;
    }
}