<?php

namespace NumPHP\Indexing;

class RavelMultiIndex
{
    public static function ravel_multi_index(array $multi_index, array $dims): array
    {
        $n = count($dims);
        $len = count($multi_index[0]);
        $out = [];
        for ($i = 0; $i < $len; $i++) {
            $idx = 0;
            $stride = 1;
            for ($d = $n - 1; $d >= 0; $d--) {
                $idx += $multi_index[$d][$i] * $stride;
                $stride *= $dims[$d];
            }
            $out[] = $idx;
        }
        return $out;
    }
}
