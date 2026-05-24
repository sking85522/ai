<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Searchsorted
{
    /**
     * Find indices where elements should be inserted to maintain order.
     *
     * @param NDArray $a
     * @param mixed $v
     * @return NDArray
     */
    public static function searchsorted(NDArray $a, $v): NDArray
    {
        $a_data = Flatten::flatten($a)->getData();
        $v_data = ($v instanceof NDArray) ? Flatten::flatten($v)->getData() : [$v];

        $indices = [];
        foreach ($v_data as $value) {
            $low = 0;
            $high = count($a_data);
            while ($low < $high) {
                $mid = (int)(($low + $high) / 2);
                if ($a_data[$mid] < $value) {
                    $low = $mid + 1;
                } else {
                    $high = $mid;
                }
            }
            $indices[] = $low;
        }

        return new NDArray($indices, 'int');
    }
}