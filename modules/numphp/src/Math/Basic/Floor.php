<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Floor
{
    public static function floor(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveFloor($data);
        return new NDArray($result, $a->getDtype());
    }

    private static function recursiveFloor($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveFloor'], $data);
        }

        return floor($data);
    }
}