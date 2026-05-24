<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Ldexp
{
    public static function ldexp(NDArray $x, $i): NDArray
    {
        $data = Helpers::mapBinary($x->getData(), ($i instanceof NDArray) ? $i->getData() : $i, function ($a, $b) {
            return $a * pow(2, $b);
        });
        return new NDArray($data);
    }
}
