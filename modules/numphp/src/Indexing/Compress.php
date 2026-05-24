<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Compress
{
    /**
     * Return selected slices of an array along given axis.
     *
     * @param NDArray $condition
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function compress(NDArray $condition, NDArray $a, ?int $axis = null): NDArray
    {
        $condData = Flatten::flatten($condition)->getData();
        $arrData = Flatten::flatten($a)->getData();

        $result = [];
        foreach ($arrData as $i => $val) {
            if (isset($condData[$i]) && $condData[$i]) {
                $result[] = $val;
            }
        }
        return new NDArray($result, $a->getDtype());
    }
}