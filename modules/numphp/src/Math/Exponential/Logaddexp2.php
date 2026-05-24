<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Logaddexp2
{
    public static function logaddexp2(NDArray $a, $b): NDArray
    {
        $data = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) {
            $m = max($x, $y);
            return $m + log(pow(2, $x - $m) + pow(2, $y - $m), 2);
        });
        return new NDArray($data);
    }
}
