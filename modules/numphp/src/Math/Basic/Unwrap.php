<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Unwrap
{
    /**
     * Unwrap by changing deltas between values to 2*pi complement.
     *
     * @param NDArray $p
     * @return NDArray
     */
    public static function unwrap(NDArray $p): NDArray
    {
        $data = Flatten::flatten($p)->getData();
        if (empty($data)) {
            return new NDArray([]);
        }

        $unwrapped = [$data[0]];
        $dd = 0;

        for ($i = 1; $i < count($data); $i++) {
            $d = $data[$i] - $data[$i - 1];
            if (abs($d) > M_PI) {
                $d_mod = fmod($d + M_PI, 2 * M_PI) - M_PI;
                if ($d_mod == -M_PI && $d > 0) {
                    $d_mod = M_PI;
                }
                $dd += $d_mod - $d;
            }
            $unwrapped[] = $data[$i] + $dd;
        }

        return new NDArray($unwrapped, 'float');
    }
}