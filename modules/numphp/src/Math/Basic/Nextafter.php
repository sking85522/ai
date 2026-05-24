<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Nextafter
{
    public static function nextafter(NDArray $x, $y): NDArray
    {
        $data = Helpers::mapBinary($x->getData(), ($y instanceof NDArray) ? $y->getData() : $y, function ($a, $b) {
            if (function_exists('nextafter')) {
                return nextafter($a, $b);
            }
            $eps = PHP_FLOAT_EPSILON * (abs($a) > 1.0 ? abs($a) : 1.0);
            return ($b > $a) ? $a + $eps : $a - $eps;
        });
        return new NDArray($data);
    }
}
