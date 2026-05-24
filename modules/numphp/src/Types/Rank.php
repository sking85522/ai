<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Rank
{
    public static function rank(NDArray $a): int
    {
        return count($a->getShape());
    }
}
