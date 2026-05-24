<?php

namespace NumPHP\Types;

use NumPHP\Core\DType;

class CanCast
{
    /**
     * Returns True if an array of dtype from can be safely cast to dtype to according to the casting rule.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public static function can_cast(string $from, string $to): bool
    {
        if ($from === $to) return true;
        if ($to === 'float' && in_array($from, ['int', 'bool'], true)) return true;
        if ($to === 'int' && $from === 'bool') return true;
        return false;
    }
}
