<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Title
{
    /**
     * For each element, return a titlecased version of the string.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function title(NDArray $a): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use (&$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return ucwords(strtolower($item));
        };
        return new NDArray($recursive_map($data), 'string');
    }
}