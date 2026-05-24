<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class MayShareMemory
{
    public static function may_share_memory(...$args)
    {
        return \NumPHP\NumPHP::may_share_memory(...$args);
    }
}
