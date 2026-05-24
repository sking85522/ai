<?php

namespace NumPHP\Math\FloatingPoint;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Isclose
{
    public static function isclose(NDArray $a, $b, float $rtol = 1e-05, float $atol = 1e-08): NDArray
    {
        $data = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) use ($rtol, $atol) {
            return abs($x - $y) <= ($atol + $rtol * abs($y));
        });
        return new NDArray($data, 'bool');
    }
}
