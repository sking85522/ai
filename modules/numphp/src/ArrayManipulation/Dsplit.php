<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Dsplit
{
    public static function dsplit(NDArray $ary, $indices_or_sections): array
    {
        $shape = $ary->getShape();
        if (count($shape) < 3) {
            throw new \Exception("dsplit only works on arrays of 3 or more dimensions");
        }
        return Split::split($ary, $indices_or_sections, 2);
    }
}