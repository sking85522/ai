<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Tri
{
    /**
     * An array with ones at and below the given diagonal and zeros elsewhere.
     *
     * @param int $N Number of rows.
     * @param int|null $M Number of columns. Defaults to N.
     * @param int $k The sub-diagonal at and below which the array is filled.
     * @param string|null $dtype
     * @return NDArray
     */
    public static function tri(int $N, ?int $M = null, int $k = 0, ?string $dtype = null): NDArray
    {
        if ($M === null) $M = $N;

        $data = [];
        for ($i = 0; $i < $N; $i++) {
            $row = [];
            for ($j = 0; $j < $M; $j++) {
                $row[] = ($j <= $i + $k) ? 1 : 0;
            }
            $data[] = $row;
        }
        return new NDArray($data, $dtype);
    }
}