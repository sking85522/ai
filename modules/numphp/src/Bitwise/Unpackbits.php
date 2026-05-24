<?php

namespace NumPHP\Bitwise;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Unpackbits
{
    public static function unpackbits(NDArray $a): NDArray
    {
        $flat = [];
        Helpers::flatten($a->getData(), $flat);
        $out = [];
        foreach ($flat as $byte) {
            for ($i = 7; $i >= 0; $i--) {
                $out[] = ($byte >> $i) & 1;
            }
        }
        return new NDArray($out, 'int');
    }
}
