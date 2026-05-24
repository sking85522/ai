<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class TrilIndicesFrom
{
    public static function tril_indices_from(NDArray $arr, int $k = 0): array
    {
        $shape = $arr->getShape();
        return TrilIndices::tril_indices($shape[0], $k);
    }
}
