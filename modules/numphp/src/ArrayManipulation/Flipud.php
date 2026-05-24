<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Flipud
{
    public static function flipud(NDArray $a): NDArray
    {
        $data = array_reverse($a->getData());
        return new NDArray($data);
    }
}
