<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Fliplr
{
    public static function fliplr(NDArray $a): NDArray
    {
        $data = $a->getData();
        $out = [];
        foreach ($data as $row) {
            $out[] = array_reverse($row);
        }
        return new NDArray($out);
    }
}
