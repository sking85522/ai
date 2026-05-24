<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Arccos
{
    public static function arccos(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveAcos($data);
        return new NDArray($resultData);
    }

    private static function recursiveAcos($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveAcos($value);
            }
            return $result;
        }
        return acos($data);
    }
}
