<?php

namespace NumPHP\Types;

class IntAlias
{
    public static function int_(...$args)
    {
        return \NumPHP\NumPHP::int_(...$args);
    }
}
