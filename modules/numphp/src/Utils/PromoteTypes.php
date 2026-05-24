<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class PromoteTypes
{
    public static function promote_types(...$args)
    {
        return \NumPHP\NumPHP::promote_types(...$args);
    }
}
