<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class DiagIndicesFrom
{
    public static function diag_indices_from(NDArray $arr): array
    {
        $shape = $arr->getShape();
        return DiagIndices::diag_indices($shape[0]);
    }
}
