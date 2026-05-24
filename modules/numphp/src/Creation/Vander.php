<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Vander
{
    /**
     * Generate a Vandermonde matrix.
     *
     * @param NDArray $x
     * @param int|null $N Number of columns in the output.
     * @param bool $increasing Order of the powers of the columns.
     * @return NDArray
     */
    public static function vander(NDArray $x, ?int $N = null, bool $increasing = false): NDArray
    {
        $data = Flatten::flatten($x)->getData();
        $m = count($data);
        if ($N === null) $N = $m;

        $result = [];
        foreach ($data as $val) {
            $row = [];
            for ($i = 0; $i < $N; $i++) {
                $power = $increasing ? $i : ($N - 1 - $i);
                $row[] = pow($val, $power);
            }
            $result[] = $row;
        }

        return new NDArray($result, $x->getDtype());
    }
}