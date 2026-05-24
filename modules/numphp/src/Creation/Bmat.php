<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Block;

class Bmat
{
    public static function bmat(array $arr): NDArray
    {
        $convert = function ($x) use (&$convert) {
            if ($x instanceof NDArray) {
                return $x;
            }
            if (is_array($x)) {
                // If this is a matrix (numeric array), wrap it
                if (!empty($x) && !is_array($x[0] ?? null)) {
                    return new NDArray($x);
                }
                // Otherwise, recurse into blocks
                $out = [];
                foreach ($x as $v) {
                    $out[] = $convert($v);
                }
                return $out;
            }
            return new NDArray([$x]);
        };

        $converted = $convert($arr);
        if ($converted instanceof NDArray) {
            return $converted;
        }
        return Block::block($converted);
    }
}
