<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Repeat
{
    /**
     * Repeat elements of an array.
     *
     * @param NDArray $a
     * @param int $repeats The number of repetitions for each element.
     * @return NDArray
     */
    public static function repeat(NDArray $a, int $repeats): NDArray
    {
        $flat = Flatten::flatten($a);
        $data = $flat->getData();
        if (!is_array($data)) $data = [$data];

        $result = [];
        foreach ($data as $element) {
            for ($i = 0; $i < $repeats; $i++) {
                $result[] = $element;
            }
        }

        return new NDArray($result, $a->getDtype());
    }
}