<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Degrees
{
    /**
     * Convert angles from radians to degrees.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function degrees(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveDegrees($data);
        return new NDArray($result, 'float');
    }

    private static function recursiveDegrees($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveDegrees'], $data);
        }
        return rad2deg($data);
    }
}