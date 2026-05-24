<?php

namespace NumPHP\Types;

class BoolAlias
{
    public static function bool(...$args)
    {
        return \NumPHP\NumPHP::bool(...$args);
    }
}
