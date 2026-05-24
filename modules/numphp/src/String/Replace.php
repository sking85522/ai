<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Replace
{
    /**
     * For each element in a, return a copy of the string with all occurrences of substring old replaced by new.
     *
     * @param NDArray $a
     * @param string $old
     * @param string $new
     * @return NDArray
     */
    public static function replace(NDArray $a, string $old, string $new): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use ($old, $new, &$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return str_replace($old, $new, $item);
        };
        return new NDArray($recursive_map($data), 'string');
    }
}