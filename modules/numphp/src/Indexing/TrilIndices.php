<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class TrilIndices
{
    public static function tril_indices(int $n, int $k = 0): array
    {
        $rows = [];
        $cols = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($j <= $i + $k) {
                    $rows[] = $i;
                    $cols[] = $j;
                }
            }
        }
        return [new NDArray($rows, 'int'), new NDArray($cols, 'int')];
    }
}
