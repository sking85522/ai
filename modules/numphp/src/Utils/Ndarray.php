<?php

namespace NumPHP\Utils;

class NdarrayAlias
{
    public static function ndarray(...$args)
    {
        return \NumPHP\NumPHP::ndarray(...$args);
    }
}
