<?php

namespace NumPHP\Sorting;

use NumPHP\Core\NDArray;

class Msort
{
    public static function msort(NDArray $a): NDArray
    {
        $data = $a->getData();
        if (!is_array($data)) {
            return new NDArray([$data]);
        }
        if (!is_array($data[0] ?? null)) {
            $copy = $data;
            sort($copy);
            return new NDArray($copy);
        }
        // sort along first axis (columns)
        $rows = count($data);
        $cols = count($data[0]);
        $out = $data;
        for ($c = 0; $c < $cols; $c++) {
            $col = [];
            for ($r = 0; $r < $rows; $r++) {
                $col[] = $data[$r][$c];
            }
            sort($col);
            for ($r = 0; $r < $rows; $r++) {
                $out[$r][$c] = $col[$r];
            }
        }
        return new NDArray($out);
    }
}
