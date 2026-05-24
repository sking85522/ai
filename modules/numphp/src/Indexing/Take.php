<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Take
{
    /**
     * Take elements from an array along an axis.
     *
     * @param NDArray $a
     * @param mixed $indices
     * @param int|null $axis
     * @return NDArray
     */
    public static function take(NDArray $a, $indices, ?int $axis = null): NDArray
    {
        if ($axis === null) {
            $a = Flatten::flatten($a);
        }

        $data = $a->getData();
        $indices_data = ($indices instanceof NDArray) ? $indices->getData() : (is_array($indices) ? $indices : [$indices]);

        $recursive_take = function ($arr, $idx_arr) use (&$recursive_take) {
            $res = [];
            foreach ($idx_arr as $idx) {
                if (is_array($idx)) {
                    $res[] = $recursive_take($arr, $idx);
                } else {
                    if (!isset($arr[$idx])) {
                        throw new \Exception("Index {$idx} is out of bounds");
                    }
                    $res[] = $arr[$idx];
                }
            }
            return $res;
        };

        return new NDArray($recursive_take($data, $indices_data));
    }
}