<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class DiagIndices
{
    public static function diag_indices(int $n): array
    {
        $idx = range(0, $n - 1);
        return [new NDArray($idx, 'int'), new NDArray($idx, 'int')];
    }
}
