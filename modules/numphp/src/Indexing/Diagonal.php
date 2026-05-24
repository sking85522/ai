<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class Diagonal
{
    public static function diagonal(NDArray $a): NDArray
    {
        $data = $a->getData();
        $n = min(count($data), count($data[0] ?? []));
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            $out[] = $data[$i][$i];
        }
        return new NDArray($out);
    }
}
