<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Flatten
{
    public static function flatten(NDArray $a): NDArray
    {
        $data = $a->getData();
        $flatData = self::recursiveFlatten($data);
        return new NDArray($flatData, $a->getDtype());
    }

    private static function recursiveFlatten($data): array
    {
        $result = [];
        if (!is_array($data)) {
            return [$data];
        }

        foreach ($data as $element) {
            if (is_array($element)) {
                $result = array_merge($result, self::recursiveFlatten($element));
            } else {
                $result[] = $element;
            }
        }

        return $result;
    }
}