<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class MaskIndices
{
    public static function mask_indices(int $n, callable $maskfunc): array
    {
        $data = [];
        for ($i = 0; $i < $n; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                $row[] = 1;
            }
            $data[] = $row;
        }
        $mask = $maskfunc(new NDArray($data));
        if ($mask instanceof NDArray) {
            $mask = $mask->getData();
        }
        $rows = [];
        $cols = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($mask[$i][$j]) {
                    $rows[] = $i;
                    $cols[] = $j;
                }
            }
        }
        return [new NDArray($rows, 'int'), new NDArray($cols, 'int')];
    }
}
