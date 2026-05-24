<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Extract
{
    public static function extract(NDArray $condition, NDArray $arr): NDArray
    {
        $cond = Flatten::flatten($condition)->getData();
        $data = Flatten::flatten($arr)->getData();
        $out = [];
        $n = min(count($cond), count($data));
        for ($i = 0; $i < $n; $i++) {
            if ($cond[$i]) $out[] = $data[$i];
        }
        return new NDArray($out);
    }
}
