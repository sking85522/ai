<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

/**
 * Legacy compatibility wrapper kept in Var.php for projects that ship this file.
 * Safe class name avoids PHP reserved-keyword parse errors.
 */
class VarianceAlias
{
    public static function var(NDArray $a, ?int $axis = null)
    {
        return Var_::var($a, $axis);
    }
}
