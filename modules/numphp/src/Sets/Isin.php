<?php

namespace NumPHP\Sets;

use NumPHP\Core\NDArray;

class Isin
{
    /**
     * Calculates if each element of an array is also present in a second array.
     *
     * @param NDArray $element
     * @param NDArray $test_elements
     * @return NDArray
     */
    public static function isin(NDArray $element, NDArray $test_elements): NDArray
    {
        $data = $element->getData();
        $test_data = \NumPHP\ArrayManipulation\Flatten::flatten($test_elements)->getData();
        $test_set = array_flip($test_data);

        $recursive_map = function ($item) use ($test_set, &$recursive_map) {
            if (is_array($item)) {
                return array_map($recursive_map, $item);
            }
            return isset($test_set[$item]);
        };

        return new NDArray($recursive_map($data), 'bool');
    }
}