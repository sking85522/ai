<?php

namespace NumPHP\Types;

class VoidAlias
{
    public static function void(...$args)
    {
        return \NumPHP\NumPHP::void(...$args);
    }
}
