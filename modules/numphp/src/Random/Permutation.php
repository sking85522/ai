<?php

namespace NumPHP\Random;

use NumPHP\Core\NDArray;

class Permutation
{
    /**
     * Return a permuted sequence or range.
     *
     * @param int|NDArray|array $x
     * @return NDArray
     */
    public static function permutation($x): NDArray
    {
        if (is_int($x)) {
            if ($x < 0) {
                throw new \InvalidArgumentException("x must be non-negative.");
            }
            $data = range(0, $x - 1);
            shuffle($data);
            return new NDArray($data, 'int');
        }

        if ($x instanceof NDArray) {
            $data = $x->getData();
        } else {
            $data = $x;
        }

        if (!is_array($data)) {
            return new NDArray([$data]);
        }

        $copy = $data;
        shuffle($copy);
        return new NDArray($copy);
    }
}
