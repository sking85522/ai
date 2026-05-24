<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class MatrixRank
{
    public static function matrix_rank(NDArray $a): int
    {
        $m = $a->getData();
        $rows = count($m);
        $cols = count($m[0] ?? []);
        $rank = 0;
        $row = 0;
        for ($col = 0; $col < $cols && $row < $rows; $col++) {
            $pivot = $row;
            while ($pivot < $rows && abs($m[$pivot][$col]) < 1e-12) {
                $pivot++;
            }
            if ($pivot == $rows) continue;
            $tmp = $m[$row];
            $m[$row] = $m[$pivot];
            $m[$pivot] = $tmp;
            $pivotVal = $m[$row][$col];
            for ($j = $col; $j < $cols; $j++) {
                $m[$row][$j] /= $pivotVal;
            }
            for ($i = 0; $i < $rows; $i++) {
                if ($i == $row) continue;
                $factor = $m[$i][$col];
                for ($j = $col; $j < $cols; $j++) {
                    $m[$i][$j] -= $factor * $m[$row][$j];
                }
            }
            $rank++;
            $row++;
        }
        return $rank;
    }
}
