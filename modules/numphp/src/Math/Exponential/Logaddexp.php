<?php

namespace NumPHP\Math\Exponential;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Logaddexp
{
    public static function logaddexp(NDArray $a, $b): NDArray
    {
        $data = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) {
            $m = max($x, $y);
            return $m + log(exp($x - $m) + exp($y - $m));
        });
        return new NDArray($data);
    }
}
