<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class Nonzero
{
    /**
     * Return the indices of the elements that are non-zero.
     * Returns a tuple of arrays, one for each dimension of a, containing the indices of the non-zero elements in that dimension.
     *
     * @param NDArray $a
     * @return array
     */
    public static function nonzero(NDArray $a): array
    {
        $argwhereResult = Argwhere::argwhere($a)->getData();
        $ndim = count($a->getShape());
        if ($ndim === 0) {
            return $a->getData() ? [new NDArray([0])] : [new NDArray([])];
        }

        $transposed = [];
        if (!empty($argwhereResult)) {
            $cols = count($argwhereResult[0]);
            for ($i = 0; $i < $cols; $i++) {
                $transposed[] = array_column($argwhereResult, $i);
            }
        } else {
            for ($i = 0; $i < $ndim; $i++) {
                $transposed[] = [];
            }
        }

        return array_map(function($indices) { return new NDArray($indices, 'int'); }, $transposed);
    }
}