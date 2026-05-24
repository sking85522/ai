<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Digitize
{
    /**
     * Return the indices of the bins to which each value in input array belongs.
     *
     * @param NDArray $x
     * @param NDArray $bins
     * @return NDArray
     */
    public static function digitize(NDArray $x, NDArray $bins): NDArray
    {
        $x_data = \NumPHP\ArrayManipulation\Flatten::flatten($x)->getData();
        $bins_data = $bins->getData();

        $result = [];
        foreach ($x_data as $val) {
            $index = 0;
            foreach ($bins_data as $bin_edge) {
                if ($val >= $bin_edge) {
                    $index++;
                }
            }
            $result[] = $index;
        }
        return new NDArray($result, 'int');
    }
}