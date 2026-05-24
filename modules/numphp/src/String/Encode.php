<?php

namespace NumPHP\String;

use NumPHP\Core\NDArray;

class Encode
{
    /**
     * Calls str.encode element-wise.
     *
     * @param NDArray $a
     * @param string $encoding
     * @return NDArray
     */
    public static function encode(NDArray $a, string $encoding = 'UTF-8'): NDArray
    {
        $data = $a->getData();
        $recursive_map = function ($item) use ($encoding, &$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return mb_convert_encoding($item, $encoding, 'UTF-8');
        };
        return new NDArray($recursive_map($data), 'string');
    }
}