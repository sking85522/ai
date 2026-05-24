<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Correlate
{
    public static function correlate(NDArray $a, NDArray $v): NDArray
    {
        $x = Flatten::flatten($a)->getData();
        $y = Flatten::flatten($v)->getData();
        $n = count($x);
        $m = count($y);
        $out = [];
        for ($i = 0; $i <= $n - $m; $i++) {
            $sum = 0.0;
            for ($j = 0; $j < $m; $j++) {
                $sum += $x[$i + $j] * $y[$j];
            }
            $out[] = $sum;
        }
        return new NDArray($out);
    }
}
