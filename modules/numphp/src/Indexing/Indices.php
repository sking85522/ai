<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class Indices
{
    public static function indices(array $dimensions): array
    {
        $n = count($dimensions);
        $out = [];
        for ($axis = 0; $axis < $n; $axis++) {
            $grid = self::buildIndexGrid($dimensions, $axis, []);
            $out[] = new NDArray($grid, 'int');
        }
        return $out;
    }

    private static function buildIndexGrid(array $dims, int $axis, array $idx)
    {
        if (count($idx) === count($dims)) {
            return $idx[$axis];
        }
        $dim = $dims[count($idx)];
        $out = [];
        for ($i = 0; $i < $dim; $i++) {
            $idx2 = $idx;
            $idx2[] = $i;
            $out[] = self::buildIndexGrid($dims, $axis, $idx2);
        }
        return $out;
    }
}
