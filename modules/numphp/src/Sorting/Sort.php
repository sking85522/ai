<?php

namespace NumPHP\Sorting;

use NumPHP\Core\NDArray;

class Sort
{
    /**
     * Sort an array, returning a sorted copy.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function sort(NDArray $a, ?int $axis = -1): NDArray
    {
        $data = $a->getData();
        $ndim = count($a->getShape());

        if ($axis === null) {
            $axis = -1;
        }

        if ($ndim <= 1) {
            $flat = is_array($data) ? $data : [$data];
            sort($flat);
            return new NDArray($flat);
        }

        if ($ndim === 2 && ($axis === -1 || $axis === 1)) {
            $sorted = [];
            foreach ($data as $row) {
                $rowCopy = $row;
                sort($rowCopy);
                $sorted[] = $rowCopy;
            }
            return new NDArray($sorted);
        }

        throw new \Exception("sort with axis not implemented for this shape.");
    }
}
