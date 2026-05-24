<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class FillDiagonal
{
    /**
     * Fill the main diagonal of a 2-D array with a value.
     *
     * @param NDArray $a
     * @param mixed $val
     * @return void
     */
    public static function fill_diagonal(NDArray $a, $val): void
    {
        $data = $a->getData();
        if (!is_array($data) || !is_array($data[0] ?? null)) {
            throw new \InvalidArgumentException("fill_diagonal expects a 2-D array.");
        }

        $rows = count($data);
        $cols = count($data[0]);
        $n = min($rows, $cols);
        for ($i = 0; $i < $n; $i++) {
            $data[$i][$i] = $val;
        }

        $a->setData($data);
    }
}
