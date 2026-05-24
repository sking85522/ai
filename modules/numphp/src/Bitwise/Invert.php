<?php

namespace NumPHP\Bitwise;

use NumPHP\Core\NDArray;

class Invert
{
    public static function invert(NDArray $a): NDArray
    {
        $data = $a->getData();
        $rec = function ($item) use (&$rec) {
            if (is_array($item)) {
                return array_map($rec, $item);
            }
            return ~$item;
        };

        return new NDArray($rec($data), 'int');
    }
}