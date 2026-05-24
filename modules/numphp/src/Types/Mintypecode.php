<?php

namespace NumPHP\Types;

class Mintypecode
{
    public static function mintypecode(array $types): string
    {
        return in_array('float', $types, true) ? 'float' : 'int';
    }
}
