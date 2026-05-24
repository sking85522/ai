<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class TrimZeros
{
    /**
     * Trim the leading and/or trailing zeros from a 1-D array or sequence.
     *
     * @param NDArray $a
     * @param string $trim 'f' for front, 'b' for back, 'fb' for both.
     * @return NDArray
     */
    public static function trim_zeros(NDArray $a, string $trim = 'fb'): NDArray
    {
        $data = Flatten::flatten($a)->getData();
        $first = 0;
        if (strpos($trim, 'f') !== false) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i] != 0) {
                    $first = $i;
                    break;
                }
                if ($i === count($data) - 1) { // all zeros
                    return new NDArray([]);
                }
            }
        }
        $last = count($data);
        if (strpos($trim, 'b') !== false) {
            for ($i = count($data) - 1; $i >= 0; $i--) {
                if ($data[$i] != 0) {
                    $last = $i + 1;
                    break;
                }
            }
        }
        $sliced = array_slice($data, $first, $last - $first);
        return new NDArray($sliced, $a->getDtype());
    }
}