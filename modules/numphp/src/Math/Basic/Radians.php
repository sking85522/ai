<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Radians
{
    /**
     * Convert angles from degrees to radians.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function radians(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveRadians($data);
        return new NDArray($result, 'float');
    }

    private static function recursiveRadians($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveRadians'], $data);
        }
        return deg2rad($data);
    }
}