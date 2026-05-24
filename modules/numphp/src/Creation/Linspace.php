<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Linspace
{
    public static function linspace($start, $stop, $num = 50, $endpoint = true, $retstep = false, $dtype = null): NDArray
    {
        $data = [];
        if ($endpoint) {
            $step = ($stop - $start) / ($num - 1);
            for ($i = 0; $i < $num; $i++) {
                $data[] = $start + ($i * $step);
            }
        } else {
            $step = ($stop - $start) / $num;
            for ($i = 0; $i < $num; $i++) {
                $data[] = $start + ($i * $step);
            }
        }

        if ($retstep) {
            return [$data, $step];
        }

        return new NDArray($data, $dtype);
    }
}