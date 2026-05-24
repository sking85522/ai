<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Tril
{
    public static function tril(NDArray $m, int $k = 0): NDArray
    {
        $data = $m->getData();
        $rows = count($data);
        $cols = count($data[0]);
        $out = [];
        for ($i = 0; $i < $rows; $i++) {
            $row = [];
            for ($j = 0; $j < $cols; $j++) {
                $row[] = ($j <= $i + $k) ? $data[$i][$j] : 0;
            }
            $out[] = $row;
        }
        return new NDArray($out);
    }
}
