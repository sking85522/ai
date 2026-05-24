<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;

class Dtype
{
    public static function dtype(NDArray $a): string
    {
        return $a->getDType();
    }
}
