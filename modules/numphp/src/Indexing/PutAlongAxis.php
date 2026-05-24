<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class PutAlongAxis
{
    public static function put_along_axis(NDArray &$arr, NDArray $indices, NDArray $values, int $axis): void
    {
        $data = $arr->getData();
        $idx = $indices->getData();
        $vals = $values->getData();
        if ($axis === 1) {
            foreach ($data as $i => $row) {
                foreach ($idx[$i] as $j => $col) {
                    $data[$i][$col] = $vals[$i][$j];
                }
            }
        } else {
            foreach ($idx as $i => $row) {
                foreach ($row as $j => $r) {
                    $data[$r][$j] = $vals[$i][$j];
                }
            }
        }
        $arr->setData($data);
    }
}
