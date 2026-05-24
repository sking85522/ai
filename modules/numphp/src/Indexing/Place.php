<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Place
{
    public static function place(NDArray &$arr, NDArray $mask, $vals): void
    {
        $data = Flatten::flatten($arr)->getData();
        $m = Flatten::flatten($mask)->getData();
        $valsArr = is_array($vals) ? $vals : [$vals];
        $vcount = count($valsArr);
        $vi = 0;
        $n = min(count($data), count($m));
        for ($i = 0; $i < $n; $i++) {
            if ($m[$i]) {
                $data[$i] = $valsArr[$vi % $vcount];
                $vi++;
            }
        }
        $arr->setData($data);
    }
}
