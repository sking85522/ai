<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class ExpandDims
{
    /**
     * Expand the shape of an array.
     * Insert a new axis that will appear at the axis position in the expanded array shape.
     *
     * @param NDArray $a
     * @param int $axis
     * @return NDArray
     */
    public static function expand_dims(NDArray $a, int $axis): NDArray
    {
        $data = $a->getData();
        $newData = self::wrap($data, $axis);
        return new NDArray($newData);
    }

    private static function wrap($data, int $depth)
    {
        if ($depth === 0) {
            return [$data];
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException("Cannot expand dims on a scalar at axis > 0");
        }
        $result = [];
        foreach ($data as $item) {
            $result[] = self::wrap($item, $depth - 1);
        }
        return $result;
    }
}