<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Clip
{
    /**
     * Clip (limit) the values in an array.
     *
     * @param NDArray $a
     * @param float|int|null $min
     * @param float|int|null $max
     * @return NDArray
     */
    public static function clip(NDArray $a, $min, $max): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveClip($data, $min, $max);
        return new NDArray($result, $a->getDtype());
    }

    private static function recursiveClip($data, $min, $max)
    {
        if (is_array($data)) {
            $func = function ($val) use ($min, $max) {
                return self::recursiveClip($val, $min, $max);
            };
            return array_map($func, $data);
        }

        if ($min !== null && $data < $min) {
            return $min;
        }

        if ($max !== null && $data > $max) {
            return $max;
        }

        return $data;
    }
}