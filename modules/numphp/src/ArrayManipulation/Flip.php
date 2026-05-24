<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Flip
{
    /**
     * Reverse the order of elements in an array along the given axis.
     * The shape of the array is preserved.
     *
     * @param NDArray $m
     * @param int|null $axis
     * @return NDArray
     */
    public static function flip(NDArray $m, ?int $axis = null): NDArray
    {
        $data = $m->getData();
        $shape = $m->getShape();

        // Case 1: 1D Array
        if (count($shape) === 1) {
            return new NDArray(array_reverse($data), $m->getDtype());
        }

        // Case 2: 2D Array
        if (count($shape) === 2) {
            // Axis 0: Flip rows (up/down)
            if ($axis === 0) {
                return new NDArray(array_reverse($data), $m->getDtype());
            }
            // Axis 1: Flip columns (left/right)
            if ($axis === 1) {
                $newData = array_map('array_reverse', $data);
                return new NDArray($newData, $m->getDtype());
            }
            // Axis null: Flip both
            if ($axis === null) {
                $reversedRows = array_reverse($data);
                $newData = array_map('array_reverse', $reversedRows);
                return new NDArray($newData, $m->getDtype());
            }
        }

        throw new \Exception("Flip currently implemented for 1D and 2D arrays (axis 0, 1 or null).");
    }
}