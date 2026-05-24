<?php

namespace NumPHP\Bitwise;

class BinaryRepr
{
    public static function binary_repr(int $num, ?int $width = null): string
    {
        $s = decbin($num);
        if ($width !== null) {
            $s = str_pad($s, $width, '0', STR_PAD_LEFT);
        }
        return $s;
    }
}
