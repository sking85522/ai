<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Cos
{
    public static function cos(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveCos($data);
        return new NDArray($resultData);
    }

    private static function recursiveCos($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveCos($value);
            }
            return $result;
        }
        return cos($data);
    }
}