<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Amax
{
    public static function amax(NDArray $a, ?int $axis = null)
    {
        return Max::max($a, $axis);
    }
}
