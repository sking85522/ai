<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Rot90
{
    public static function rot90(NDArray $m, int $k = 1): NDArray
    {
        $k = $k % 4;
        $data = $m->getData();
        if ($k < 0) $k += 4;
        $out = $data;
        for ($i = 0; $i < $k; $i++) {
            $out = self::rot90once($out);
        }
        return new NDArray($out);
    }

    private static function rot90once($data)
    {
        $rows = count($data);
        $cols = count($data[0]);
        $out = [];
        for ($c = $cols - 1; $c >= 0; $c--) {
            $row = [];
            for ($r = 0; $r < $rows; $r++) {
                $row[] = $data[$r][$c];
            }
            $out[] = $row;
        }
        return $out;
    }
}
