<?php

namespace NumPHP\Sorting;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Argsort
{
    /**
     * Returns the indices that would sort an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function argsort(NDArray $a, ?int $axis = -1): NDArray
    {
        $ndim = count($a->getShape());
        if ($axis === null) {
            $axis = -1;
        }

        if ($ndim <= 1) {
            $data = Flatten::flatten($a)->getData();
            asort($data);
            return new NDArray(array_keys($data), 'int');
        }

        if ($ndim === 2 && ($axis === -1 || $axis === 1)) {
            $result = [];
            foreach ($a->getData() as $row) {
                $indexed = $row;
                asort($indexed);
                $result[] = array_keys($indexed);
            }
            return new NDArray($result, 'int');
        }

        throw new \Exception("argsort with axis not implemented for this shape.");
    }
}
