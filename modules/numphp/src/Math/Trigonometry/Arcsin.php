<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Arcsin
{
    public static function arcsin(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveAsin($data);
        return new NDArray($resultData);
    }

    private static function recursiveAsin($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveAsin($value);
            }
            return $result;
        }
        return asin($data);
    }
}
