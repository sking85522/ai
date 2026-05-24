<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanargmin
{
    public static function nanargmin(NDArray $a)
    {
        $data = Flatten::flatten($a)->getData();
        $min = null;
        $idx = -1;
        foreach ($data as $i => $val) {
            if (is_nan($val)) continue;
            if ($min === null || $val < $min) {
                $min = $val;
                $idx = $i;
            }
        }
        return $idx;
    }
}
