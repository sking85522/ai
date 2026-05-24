<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Tan
{
    public static function tan(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveTan($data);
        return new NDArray($resultData);
    }

    private static function recursiveTan($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveTan($value);
            }
            return $result;
        }
        return tan($data);
    }
}