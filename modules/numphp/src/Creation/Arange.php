<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Arange
{
    public static function arange($start, $stop = null, $step = 1, $dtype = null): NDArray
    {
        if (is_null($stop)) {
            $stop = $start;
            $start = 0;
        }

        $data = range($start, $stop, $step);
        return new NDArray($data, $dtype);
    }
}