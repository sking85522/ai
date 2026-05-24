<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Power
{
    public static function power(NDArray $a, $exponent): NDArray
    {
        $data = $a->getData();
        $result = self::recursivePower($data, $exponent);
        return new NDArray($result, $a->getDtype());
    }

    private static function recursivePower($data, $exponent)
    {
        if (is_array($data)) {
            $func = function($val) use ($exponent) {
                return self::recursivePower($val, $exponent);
            };
            return array_map($func, $data);
        }
        // Handle NDArray exponent in future, for now scalar exponent
        return pow($data, $exponent);
    }
}