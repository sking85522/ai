<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Roll
{
    /**
     * Roll array elements along a given axis.
     * Currently supports 1D arrays or flattened arrays.
     *
     * @param NDArray $a
     * @param int $shift The number of places by which elements are shifted.
     * @return NDArray
     */
    public static function roll(NDArray $a, int $shift): NDArray
    {
        // Flatten for v1
        $data = Flatten::flatten($a)->getData();
        $count = count($data);
        if ($count === 0) {
            return new NDArray([], $a->getDtype());
        }

        $shift = $shift % $count;
        if ($shift < 0) $shift += $count;

        $part2 = array_splice($data, 0, $count - $shift);
        return new NDArray(array_merge($data, $part2), $a->getDtype());
    }
}