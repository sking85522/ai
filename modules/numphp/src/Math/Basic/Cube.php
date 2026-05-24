<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Cube
{
    /**
     * Return the cube of the input, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function cube(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveCube($data);
        return new NDArray($resultData);
    }

    private static function recursiveCube($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveCube($value);
            }
            return $result;
        }
        return $data * $data * $data;
    }
}
