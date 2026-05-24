<?php

namespace NumPHP\ArrayManipulation;
use NumPHP\Core\NDArray;

class Vsplit
{
    /**
     * Split an array into multiple sub-arrays vertically (row-wise).
     *
     * @param NDArray $ary
     * @param int|array $indices_or_sections
     * @return array
     */
    public static function vsplit(NDArray $ary, $indices_or_sections): array {
        return Split::split($ary, $indices_or_sections, 0);
    }
}