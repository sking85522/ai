<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Delete
{
    /**
     * Return a new array with sub-arrays along an axis deleted.
     * For a one dimensional array, this returns those entries not returned by arr[obj].
     *
     * @param NDArray $arr
     * @param int|array $obj Indices to remove.
     * @param int|null $axis
     * @return NDArray
     */
    public static function delete(NDArray $arr, $obj, ?int $axis = null): NDArray
    {
        if ($axis === null) {
            $flat = Flatten::flatten($arr)->getData();
            $indices = is_array($obj) ? $obj : [$obj];

            foreach ($indices as $idx) {
                unset($flat[$idx]);
            }

            return new NDArray(array_values($flat), $arr->getDtype());
        }

        throw new \Exception("Delete with specific axis not yet implemented.");
    }
}