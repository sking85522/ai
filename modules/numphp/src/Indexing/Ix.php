<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class Ix
{
    public static function ix_(...$arrays): array
    {
        $out = [];
        $n = count($arrays);
        for ($i = 0; $i < $n; $i++) {
            $arr = $arrays[$i] instanceof NDArray ? $arrays[$i]->getData() : $arrays[$i];
            $shape = array_fill(0, $n, 1);
            $shape[$i] = count($arr);
            $grid = self::reshape($arr, $shape, $i);
            $out[] = new NDArray($grid, 'int');
        }
        return $out;
    }

    private static function reshape($arr, $shape, $axis)
    {
        if (count($shape) === 1) {
            return $arr;
        }
        $dim = array_shift($shape);
        $out = [];
        for ($i = 0; $i < $dim; $i++) {
            $out[] = self::reshape($arr, $shape, $axis);
        }
        return $out;
    }
}
