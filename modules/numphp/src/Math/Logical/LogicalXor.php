<?php

namespace NumPHP\Math\Logical;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class LogicalXor
{
    public static function logical_xor(NDArray $a, $b): NDArray
    {
        $data = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) {
            return (bool) $x xor (bool) $y;
        });
        return new NDArray($data, 'bool');
    }
}
