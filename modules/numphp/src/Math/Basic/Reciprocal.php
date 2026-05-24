<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Reciprocal
{
    /**
     * Return the reciprocal of the argument, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function reciprocal(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveReciprocal($data);
        return new NDArray($result, 'float'); // Division usually results in float
    }

    private static function recursiveReciprocal($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveReciprocal'], $data);
        }
        if ($data == 0) return INF; // Or handle as warning/exception similar to NumPy
        return 1 / $data;
    }
}