<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Sin
{
    public static function sin(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveSin($data);
        return new NDArray($resultData);
    }

    private static function recursiveSin($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveSin($value);
            }
            return $result;
        }
        return sin($data);
    }
}