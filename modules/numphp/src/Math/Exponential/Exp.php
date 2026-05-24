<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;

class Exp
{
    public static function exp(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveExp($data);
        return new NDArray($resultData);
    }

    private static function recursiveExp($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveExp($value);
            }
            return $result;
        }
        return exp($data);
    }
}