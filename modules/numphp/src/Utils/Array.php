<?php

namespace NumPHP\Utils;

/**
 * Safe wrapper retained in legacy file path.
 */
class ArrayAlias
{
    public static function array(...$args)
    {
        return \NumPHP\NumPHP::array(...$args);
    }
}
