<?php

namespace NumPHP\Types;

class PromoteTypes
{
    public static function promote_types(string $type1, string $type2): string
    {
        if ($type1 === 'float' || $type2 === 'float') return 'float';
        if ($type1 === 'bool' && $type2 === 'bool') return 'bool';
        return 'int';
    }
}
