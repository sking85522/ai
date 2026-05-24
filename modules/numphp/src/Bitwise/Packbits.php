<?php

namespace NumPHP\Bitwise;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Packbits
{
    public static function packbits(NDArray $a): NDArray
    {
        $flat = [];
        Helpers::flatten($a->getData(), $flat);
        $out = [];
        $byte = 0;
        $count = 0;
        foreach ($flat as $bit) {
            $byte = ($byte << 1) | ((int) $bit & 1);
            $count++;
            if ($count === 8) {
                $out[] = $byte;
                $byte = 0;
                $count = 0;
            }
        }
        if ($count > 0) {
            $byte = $byte << (8 - $count);
            $out[] = $byte;
        }
        return new NDArray($out, 'int');
    }
}
