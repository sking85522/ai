<?php

namespace NumPHP\Types;

class Obj2Sctype
{
    public static function obj2sctype($obj): string
    {
        return is_float($obj) ? 'float' : (is_bool($obj) ? 'bool' : 'int');
    }
}
