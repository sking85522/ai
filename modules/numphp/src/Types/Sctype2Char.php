<?php

namespace NumPHP\Types;

class Sctype2Char
{
    public static function sctype2char(string $dtype): string
    {
        return $dtype === 'float' ? 'f' : ($dtype === 'bool' ? 'b' : 'i');
    }
}
