<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Lower
{
    /**
     * Return an array with the elements converted to lowercase.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function lower(NDArray $a): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use (&$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return strtolower($item);
        };
        return new NDArray($recursive_map($data), 'string');
    }
}