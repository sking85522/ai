<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class I0
{
    public static function i0(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) {
            $sum = 1.0;
            $y = ($x * $x) / 4.0;
            $t = 1.0;
            for ($k = 1; $k <= 10; $k++) {
                $t *= $y / ($k * $k);
                $sum += $t;
            }
            return $sum;
        });
        return new NDArray($data);
    }
}
