<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanargmax
{
    public static function nanargmax(NDArray $a)
    {
        $data = Flatten::flatten($a)->getData();
        $max = null;
        $idx = -1;
        foreach ($data as $i => $val) {
            if (is_nan($val)) continue;
            if ($max === null || $val > $max) {
                $max = $val;
                $idx = $i;
            }
        }
        return $idx;
    }
}
