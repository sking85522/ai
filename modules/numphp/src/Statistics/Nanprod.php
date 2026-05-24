<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanprod
{
    public static function nanprod(NDArray $a)
    {
        $data = Flatten::flatten($a)->getData();
        $prod = 1.0;
        $has = false;
        foreach ($data as $val) {
            if (is_nan($val)) continue;
            $has = true;
            $prod *= $val;
        }
        return $has ? $prod : NAN;
    }
}
