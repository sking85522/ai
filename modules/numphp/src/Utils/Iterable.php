<?php

namespace NumPHP\Utils;

class IterableAlias
{
    public static function iterable(...$args)
    {
        return \NumPHP\NumPHP::iterable(...$args);
    }
}
