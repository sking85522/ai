<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Triu
{
    public static function triu(NDArray $m, int $k = 0): NDArray
    {
        $data = $m->getData();
        if (!is_array($data) || !is_array($data[0] ?? null)) {
            return $m;
        }
        $rows = count($data);
        $cols = count($data[0]);
        $out = [];
        for ($i = 0; $i < $rows; $i++) {
            $row = [];
            for ($j = 0; $j < $cols; $j++) {
                $row[] = ($j >= $i + $k) ? $data[$i][$j] : 0;
            }
            $out[] = $row;
        }
        return new NDArray($out);
    }
}
