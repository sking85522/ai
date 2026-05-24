<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Moveaxis
{
    public static function moveaxis(NDArray $a, int $source, int $destination): NDArray
    {
        return Swapaxes::swapaxes($a, $source, $destination);
    }
}
