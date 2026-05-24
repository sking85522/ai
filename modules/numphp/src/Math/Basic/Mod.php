<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Mod
{
    public static function mod(NDArray $a, $b): NDArray
    {
        $data = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) {
            return $x - floor($x / $y) * $y;
        });
        return new NDArray($data);
    }
}
