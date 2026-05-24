<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Diagflat
{
    public static function diagflat(NDArray $a): NDArray
    {
        $flat = Flatten::flatten($a)->getData();
        $n = count($flat);
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            $row = array_fill(0, $n, 0);
            $row[$i] = $flat[$i];
            $out[] = $row;
        }
        return new NDArray($out);
    }
}
