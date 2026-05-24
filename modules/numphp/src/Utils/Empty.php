<?php

namespace NumPHP\Utils;

class EmptyAlias
{
    public static function empty(...$args)
    {
        return \NumPHP\NumPHP::empty(...$args);
    }
}
