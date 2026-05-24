<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class TriuIndicesFrom
{
    public static function triu_indices_from(NDArray $arr, int $k = 0): array
    {
        $shape = $arr->getShape();
        return TriuIndices::triu_indices($shape[0], $k);
    }
}
