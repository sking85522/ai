<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Capitalize
{
    /**
     * Return a copy of the array with only the first character of each element capitalized.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function capitalize(NDArray $a): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use (&$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return ucfirst(strtolower($item));
        };
        return new NDArray($recursive_map($data), 'string');
    }
}