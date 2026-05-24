<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Slogdet
{
    public static function slogdet(NDArray $a): array
    {
        $det = Determinant::det($a);
        $sign = ($det > 0) ? 1 : (($det < 0) ? -1 : 0);
        $logabs = ($det == 0) ? -INF : log(abs($det));
        return [new NDArray($sign), new NDArray($logabs)];
    }
}
