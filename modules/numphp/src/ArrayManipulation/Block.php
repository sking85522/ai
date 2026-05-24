<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Block
{
    public static function block(array $arrays): NDArray
    {
        // Simplified implementation: Assumes list of lists of 2D arrays
        // [[A, B], [C, D]] -> vstack([hstack([A, B]), hstack([C, D])])

        if (!is_array($arrays[0])) {
            // 1D list of arrays -> hstack
            return Hstack::hstack($arrays);
        }

        $rows = [];
        foreach ($arrays as $rowArrs) {
            $rows[] = Hstack::hstack($rowArrs);
        }

        return Vstack::vstack($rows);
    }
}