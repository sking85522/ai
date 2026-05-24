<?php

namespace NumPHP\Types;

class Iinfo
{
    public static function iinfo(string $dtype)
    {
        return (object)[
            'max' => PHP_INT_MAX,
            'min' => PHP_INT_MIN,
            'bits' => 64
        ];
    }
}
