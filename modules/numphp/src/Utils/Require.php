<?php

namespace NumPHP\Utils;

class RequireAlias
{
    public static function require(...$args)
    {
        return \NumPHP\NumPHP::require(...$args);
    }
}
