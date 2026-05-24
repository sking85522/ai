<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Ceil
{
    public static function ceil(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveCeil($data);
        return new NDArray($result, $a->getDtype());
    }

    private static function recursiveCeil($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveCeil'], $data);
        }

        return ceil($data);
    }
}