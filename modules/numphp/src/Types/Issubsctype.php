<?php

namespace NumPHP\Types;

class Issubsctype
{
    public static function issubsctype(string $dtype, string $kind): bool
    {
        return $dtype === $kind;
    }
}
