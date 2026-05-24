<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Amin
{
    public static function amin(NDArray $a, ?int $axis = null)
    {
        return Min::min($a, $axis);
    }
}
