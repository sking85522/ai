<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Append
{
    /**
     * Append values to the end of an array.
     *
     * @param NDArray $arr
     * @param mixed $values
     * @param int|null $axis The axis along which values are appended. If axis is not given, both arr and values are flattened before use.
     * @return NDArray
     */
    public static function append(NDArray $arr, $values, ?int $axis = null): NDArray
    {
        if ($axis === null) {
            $flatArr = Flatten::flatten($arr);
            $valuesArr = ($values instanceof NDArray) ? $values : new NDArray($values);
            $flatValues = Flatten::flatten($valuesArr);
            return Concatenate::concatenate([$flatArr, $flatValues], 0);
        }

        throw new \Exception("Append with specific axis not yet implemented.");
    }
}