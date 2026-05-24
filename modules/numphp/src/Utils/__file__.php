<?php

namespace NumPHP\Utils;

class FileAlias
{
    public static function fileValue(...$args)
    {
        return \NumPHP\NumPHP::__file__(...$args);
    }
}
