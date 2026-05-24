<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Flatnonzero
{
    public static function flatnonzero(NDArray $a): NDArray
    {
        $data = Flatten::flatten($a)->getData();
        $idx = [];
        foreach ($data as $i => $v) {
            if ($v) $idx[] = $i;
        }
        return new NDArray($idx, 'int');
    }
}
