<?php

namespace NumPHP\Types;

class Issubdtype
{
    public static function issubdtype(string $dtype, string $kind): bool
    {
        return $dtype === $kind;
    }
}
