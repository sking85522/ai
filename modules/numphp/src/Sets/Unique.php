<?php

namespace NumPHP\Sets;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Unique
{
    /**
     * Find the unique elements of an array.
     * Returns the sorted unique elements of an array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function unique(NDArray $a): NDArray
    {
        // NumPy's unique flattens the array first if no axis is specified
        $flat = Flatten::flatten($a);
        $data = $flat->getData();
        if (!is_array($data)) $data = [$data];

        $unique = array_unique($data);
        sort($unique);

        return new NDArray(array_values($unique), $a->getDtype());
    }
}
