<?php

namespace NumPHP\Sets;

use NumPHP\Core\NDArray;

class Setxor1D
{
    public static function setxor1d(NDArray $ar1, NDArray $ar2): NDArray
    {
        $a = array_values(array_unique($ar1->getData()));
        $b = array_values(array_unique($ar2->getData()));
        $out = array_values(array_merge(array_diff($a, $b), array_diff($b, $a)));
        sort($out);
        return new NDArray($out);
    }
}
