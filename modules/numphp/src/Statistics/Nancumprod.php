<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nancumprod
{
    public static function nancumprod(NDArray $a): NDArray
    {
        $data = Flatten::flatten($a)->getData();
        $out = [];
        $prod = 1.0;
        foreach ($data as $val) {
            if (is_nan($val)) {
                $out[] = $prod;
                continue;
            }
            $prod *= $val;
            $out[] = $prod;
        }
        return new NDArray($out);
    }
}
