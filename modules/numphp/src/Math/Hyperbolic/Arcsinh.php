<?php

namespace NumPHP\Math\Hyperbolic;

use NumPHP\Core\NDArray;

class Arcsinh
{
    public static function arcsinh(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveAsinh($data);
        return new NDArray($resultData);
    }

    private static function recursiveAsinh($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveAsinh($value);
            }
            return $result;
        }
        return asinh($data);
    }
}
