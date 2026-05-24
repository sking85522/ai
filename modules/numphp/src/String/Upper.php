<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Upper
{
    /**
     * Return an array with the elements converted to uppercase.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function upper(NDArray $a): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use (&$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return strtoupper($item);
        };
        return new NDArray($recursive_map($data), 'string');
    }
}