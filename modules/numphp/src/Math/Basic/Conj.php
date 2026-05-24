<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Conj
{
    public static function conj(NDArray $a): NDArray
    {
        $data = Helpers::mapUnary($a->getData(), function ($x) { return $x; });
        return new NDArray($data);
    }
}
