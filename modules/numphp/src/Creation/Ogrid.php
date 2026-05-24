<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Ogrid
{
    public static function ogrid(array $x, array $y): array
    {
        return [new NDArray($x), new NDArray($y)];
    }
}
