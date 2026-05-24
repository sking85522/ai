<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class SetPrintoptions
{
    public static function set_printoptions(...$args)
    {
        return \NumPHP\NumPHP::set_printoptions(...$args);
    }
}
