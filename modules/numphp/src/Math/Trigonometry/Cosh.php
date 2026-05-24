<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Cosh
{
    public static function cosh(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveCosh($data);
        return new NDArray($resultData);
    }

    private static function recursiveCosh($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveCosh($value);
            }
            return $result;
        }
        return cosh($data);
    }
}
