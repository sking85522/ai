<?php

namespace NumPHP\Math\Hyperbolic;

use NumPHP\Core\NDArray;

class Tanh
{
    public static function tanh(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveTanh($data);
        return new NDArray($resultData);
    }

    private static function recursiveTanh($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveTanh($value);
            }
            return $result;
        }
        return tanh($data);
    }
}
