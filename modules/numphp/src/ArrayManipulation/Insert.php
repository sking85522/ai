<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Insert
{
    /**
     * Insert values along the given axis before the given indices.
     *
     * @param NDArray $arr
     * @param int $obj Index before which to insert.
     * @param mixed $values
     * @param int|null $axis
     * @return NDArray
     */
    public static function insert(NDArray $arr, int $obj, $values, ?int $axis = null): NDArray
    {
        if ($axis === null) {
            $flat = Flatten::flatten($arr)->getData();
            $val = ($values instanceof NDArray) ? $values->getData() : $values;
            $valArr = is_array($val) ? $val : [$val];

            array_splice($flat, $obj, 0, $valArr);

            return new NDArray($flat, $arr->getDtype());
        }

        throw new \Exception("Insert with specific axis not yet implemented.");
    }
}