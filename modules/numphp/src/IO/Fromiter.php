<?php

namespace NumPHP\IO;

use NumPHP\Core\NDArray;

class Fromiter
{
    /**
     * Create a 1-D array from an iterable.
     *
     * @param iterable $iterable
     * @param string|null $dtype
     * @param int|null $count
     * @return NDArray
     */
    public static function fromiter(iterable $iterable, string $dtype = null, ?int $count = null): NDArray
    {
        $data = [];
        $i = 0;
        foreach ($iterable as $value) {
            $data[] = $value;
            $i++;
            if ($count !== null && $i >= $count) {
                break;
            }
        }
        return new NDArray($data, $dtype);
    }
}
