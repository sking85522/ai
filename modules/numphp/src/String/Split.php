<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Split
{
    public static function split(NDArray $a, string $sep = ' '): NDArray
    {
        $data = $a->getData();
        $rec = function($item) use (&$rec, $sep) {
            if (is_array($item)) return array_map($rec, $item);
            return explode($sep, $item);
        };

        // Result will be array of arrays (lists of strings)
        return new NDArray($rec($data));
    }
}