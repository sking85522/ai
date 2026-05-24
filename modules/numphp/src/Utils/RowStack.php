<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class RowStack
{
    public static function row_stack(...$args)
    {
        return \NumPHP\NumPHP::row_stack(...$args);
    }
}
