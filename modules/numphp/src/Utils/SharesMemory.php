<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class SharesMemory
{
    public static function shares_memory(...$args)
    {
        return \NumPHP\NumPHP::shares_memory(...$args);
    }
}
