<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Eye
{
    public static function eye($N, $M = null, $k = 0, $dtype = null): NDArray
    {
        $M = $M ?? $N;
        $data = [];
        for ($i = 0; $i < $N; $i++) {
            $row = [];
            for ($j = 0; $j < $M; $j++) {
                $row[] = ($j == $i + $k) ? 1 : 0;
            }
            $data[] = $row;
        }
        return new NDArray($data, $dtype);
    }
}