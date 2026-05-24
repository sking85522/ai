<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Identity
{
    public static function identity($n, $dtype = null): NDArray
    {
        $data = [];
        for ($i = 0; $i < $n; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                $row[] = ($i == $j) ? 1 : 0;
            }
            $data[] = $row;
        }
        return new NDArray($data, $dtype);
    }
}