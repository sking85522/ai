<?php

namespace NumPHP\Types;

class Finfo
{
    public static function finfo(string $dtype)
    {
        // Return dummy object with some constants
        return (object)[
            'eps' => 2.220446049250313e-16,
            'max' => PHP_FLOAT_MAX,
            'min' => PHP_FLOAT_MIN
        ];
    }
}
