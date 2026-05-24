<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class ArraySplit
{
    public static function array_split(NDArray $ary, $indices_or_sections): array
    {
        return Split::split($ary, $indices_or_sections);
    }
}
