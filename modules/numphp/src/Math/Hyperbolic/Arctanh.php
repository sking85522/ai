<?php

namespace NumPHP\Math\Hyperbolic;

use NumPHP\Core\NDArray;

class Arctanh
{
    public static function arctanh(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveAtanh($data);
        return new NDArray($resultData);
    }

    private static function recursiveAtanh($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveAtanh($value);
            }
            return $result;
        }
        return atanh($data);
    }
}
