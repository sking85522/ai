<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Logspace
{
    public static function logspace($start, $stop, $num = 50, $base = 10.0, $endpoint = true, $dtype = null): NDArray
    {
        $y = Linspace::linspace($start, $stop, $num, $endpoint, false, null);
        $data = $y->getData();

        $powFunc = function($val) use ($base) {
            return pow($base, $val);
        };

        $result = array_map($powFunc, $data);

        return new NDArray($result, $dtype);
    }
}