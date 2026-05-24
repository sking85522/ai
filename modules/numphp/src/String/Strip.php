<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Strip
{
    /**
     * For each element in a, return a copy with the leading and trailing characters removed.
     *
     * @param NDArray $a
     * @param string $chars
     * @return NDArray
     */
    public static function strip(NDArray $a, string $chars = " \t\n\r\0\x0B"): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use ($chars, &$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return trim($item, $chars);
        };
        return new NDArray($recursive_map($data), 'string');
    }
}