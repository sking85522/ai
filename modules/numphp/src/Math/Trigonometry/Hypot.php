<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Hypot
{
    /**
     * Given the "legs" of a right triangle, return its hypotenuse.
     * Equivalent to sqrt(x1^2 + x2^2), element-wise.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function hypot(NDArray $x1, NDArray $x2): NDArray
    {
        $d1 = $x1->getData();
        $d2 = $x2->getData();
        $result = self::recursiveHypot($d1, $d2);
        return new NDArray($result, 'float');
    }

    private static function recursiveHypot($d1, $d2)
    {
        if (is_array($d1)) {
             $func = function ($a, $b) {
                return self::recursiveHypot($a, $b);
            };
            // Handling simple broadcasting if d2 is scalar not implemented here for brevity, assumes matching shapes or manual expansion
            $d2Arr = is_array($d2) ? $d2 : array_fill(0, count($d1), $d2);
            return array_map($func, $d1, $d2Arr);
        }
        return hypot($d1, $d2);
    }
}