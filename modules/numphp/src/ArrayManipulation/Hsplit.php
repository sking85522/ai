<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Hsplit
{
    /**
     * Split an array into multiple sub-arrays horizontally (column-wise).
     *
     * @param NDArray $ary
     * @param int|array $indices_or_sections
     * @return array
     */
    public static function hsplit(NDArray $ary, $indices_or_sections): array
    {
        if (count($ary->getShape()) <= 1) {
            return Split::split($ary, $indices_or_sections, 0);
        }
       return Split::split($ary, $indices_or_sections, 1);
    }
}