<?php

namespace NumPHP\Sorting;

use NumPHP\Core\NDArray;

class SortComplex
{
    public static function sort_complex(NDArray $a): NDArray
    {
        $data = $a->getData();
        if (!is_array($data)) {
            return new NDArray([$data]);
        }
        $copy = $data;
        sort($copy);
        return new NDArray($copy);
    }
}
