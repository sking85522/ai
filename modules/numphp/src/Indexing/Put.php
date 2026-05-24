<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Put
{
    public static function put(NDArray &$a, array $indices, $values): void
    {
        $data = Flatten::flatten($a)->getData();
        $vals = is_array($values) ? $values : [$values];
        $vi = 0;
        foreach ($indices as $idx) {
            $data[$idx] = $vals[$vi % count($vals)];
            $vi++;
        }
        $a->setData($data);
    }
}
