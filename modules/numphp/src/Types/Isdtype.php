<?php

namespace NumPHP\Types;

class Isdtype
{
    public static function isdtype(string $dtype, string $kind): bool
    {
        return $dtype === $kind;
    }
}
