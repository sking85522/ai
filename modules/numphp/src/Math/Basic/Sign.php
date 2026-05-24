<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Sign
{
    /**
     * Returns an element-wise indication of the sign of a number.
     * The sign of a number is -1 if x < 0, 0 if x==0, 1 if x > 0.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function sign(NDArray $a): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveSign($data);
        return new NDArray($result, $a->getDtype());
    }

    private static function recursiveSign($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'recursiveSign'], $data);
        }
        if ($data == 0) return 0;
        return ($data > 0) ? 1 : -1;
    }
}