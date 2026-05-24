<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Nanquantile
{
    public static function nanquantile(NDArray $a, float $q)
    {
        $data = Flatten::flatten($a)->getData();
        $filtered = array_values(array_filter($data, function ($v) { return !is_nan($v); }));
        if (empty($filtered)) return NAN;
        return Quantile::quantile(new NDArray($filtered), $q);
    }
}
