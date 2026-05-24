<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Seterr
{
    public static function seterr(...$args)
    {
        return \NumPHP\NumPHP::seterr(...$args);
    }
}
