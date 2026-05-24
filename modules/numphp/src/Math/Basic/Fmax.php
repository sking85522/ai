<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Fmax
{
    public static function fmax(NDArray $a, $b): NDArray
    {
        $data = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) {
            if (is_nan($x)) return $y;
            if (is_nan($y)) return $x;
            return ($x > $y) ? $x : $y;
        });
        return new NDArray($data);
    }
}
