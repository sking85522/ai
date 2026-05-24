<?php

namespace NumPHP\Utils;

use NumPHP\Core\NDArray;

class ColumnStack
{
    public static function column_stack(...$args)
    {
        return \NumPHP\NumPHP::column_stack(...$args);
    }
}
