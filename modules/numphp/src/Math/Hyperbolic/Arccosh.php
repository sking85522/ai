<?php

namespace NumPHP\Math\Hyperbolic;

use NumPHP\Core\NDArray;

class Arccosh
{
    public static function arccosh(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveAcosh($data);
        return new NDArray($resultData);
    }

    private static function recursiveAcosh($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveAcosh($value);
            }
            return $result;
        }
        return acosh($data);
    }
}
