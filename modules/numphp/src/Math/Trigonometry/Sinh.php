<?php

namespace NumPHP\Math\Trigonometry;

use NumPHP\Core\NDArray;

class Sinh
{
    public static function sinh(NDArray $a): NDArray
    {
        $data = $a->getData();
        $resultData = self::recursiveSinh($data);
        return new NDArray($resultData);
    }

    private static function recursiveSinh($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $value) {
                $result[] = self::recursiveSinh($value);
            }
            return $result;
        }
        return sinh($data);
    }
}
