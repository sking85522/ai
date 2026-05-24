<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class TakeAlongAxis
{
    public static function take_along_axis(NDArray $arr, NDArray $indices, int $axis): NDArray
    {
        $data = $arr->getData();
        $idx = $indices->getData();
        if ($axis === 0) {
            $out = [];
            foreach ($idx as $i => $row) {
                $outRow = [];
                foreach ($row as $j => $v) {
                    $outRow[] = $data[$v][$j];
                }
                $out[] = $outRow;
            }
            return new NDArray($out);
        }
        if ($axis === 1) {
            $out = [];
            foreach ($data as $i => $row) {
                $outRow = [];
                foreach ($idx[$i] as $v) {
                    $outRow[] = $row[$v];
                }
                $out[] = $outRow;
            }
            return new NDArray($out);
        }
        throw new \Exception('take_along_axis only supports axis 0 or 1 for 2D');
    }
}
