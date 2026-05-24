<?php

namespace NumPHP\Math\Calculus;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Diff
{
    public static function diff(NDArray $a, int $n = 1): NDArray
    {
        $data = Flatten::flatten($a)->getData();

        for ($i = 0; $i < $n; $i++) {
            $nextData = [];
            $count = count($data);
            if ($count <= 1) {
                $data = [];
                break;
            }
            for ($j = 0; $j < $count - 1; $j++) {
                $nextData[] = $data[$j + 1] - $data[$j];
            }
            $data = $nextData;
        }

        return new NDArray($data, $a->getDtype());
    }
}