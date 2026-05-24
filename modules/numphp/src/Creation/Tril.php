<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Tril
{
    /**
     * Lower triangle of an array.
     *
     * @param NDArray $m
     * @param int $k Diagonal above which to zero elements.
     * @return NDArray
     */
    public static function tril(NDArray $m, int $k = 0): NDArray
    {
        // Works for 2D arrays
        $data = $m->getData();
        $rows = count($data);
        $result = [];
        for ($i = 0; $i < $rows; $i++) {
            $cols = count($data[$i]);
            $newRow = [];
            for ($j = 0; $j < $cols; $j++) {
                $newRow[] = ($j <= $i + $k) ? $data[$i][$j] : 0;
            }
            $result[] = $newRow;
        }
        return new NDArray($result, $m->getDtype());
    }
}