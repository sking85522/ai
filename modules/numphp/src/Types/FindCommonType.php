<?php

namespace NumPHP\Types;

class FindCommonType
{
    public static function find_common_type(array $types): string
    {
        return in_array('float', $types, true) ? 'float' : 'int';
    }
}
