<?php

namespace NumPHP\Types;

class MinScalarType
{
    public static function min_scalar_type($obj): string
    {
        return is_float($obj) ? 'float' : (is_bool($obj) ? 'bool' : 'int');
    }
}
