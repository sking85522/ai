<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Squeeze
{
    /**
     * Remove single-dimensional entries from the shape of an array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function squeeze(NDArray $a): NDArray
    {
        $data = $a->getData();
        $newData = self::recursiveSqueeze($data);
        return new NDArray($newData);
    }

    private static function recursiveSqueeze($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        if (count($data) === 1) {
            // This is a dimension of size 1, go deeper
            return self::recursiveSqueeze($data[0]);
        }

        // This dimension is > 1, apply squeeze to all children
        return array_map([self::class, 'recursiveSqueeze'], $data);
    }
}