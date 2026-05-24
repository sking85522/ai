<?php

namespace NumPHP\Utils;

class DirAlias
{
    public static function dirValue(...$args)
    {
        return \NumPHP\NumPHP::__dir__(...$args);
    }
}
