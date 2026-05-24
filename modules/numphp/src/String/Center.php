<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Center
{
    /**
     * Return a copy of the array with its elements centered in a string of length width.
     *
     * @param NDArray $a
     * @param int $width
     * @param string $fillchar
     * @return NDArray
     */
    public static function center(NDArray $a, int $width, string $fillchar = ' '): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use ($width, $fillchar, &$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return str_pad($item, $width, $fillchar, STR_PAD_BOTH);
        };
        return new NDArray($recursive_map($data), 'string');
    }
}