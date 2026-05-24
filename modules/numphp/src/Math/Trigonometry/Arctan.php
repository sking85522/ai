<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Arctan
{
    public static function arctan(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveAtan($data);
        return new NDArray($resultData);
    }

    private static function recursiveAtan($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveAtan($value);
            }
            return $result;
        }
        return atan($data);
    }
}
