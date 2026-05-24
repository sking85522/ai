<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class Choose
{
    /**
     * Construct an array from an index array and a set of choices.
     *
     * @param NDArray $a
     * @param array $choices
     * @return NDArray
     */
    public static function choose(NDArray $a, array $choices): NDArray
    {
        $a_data = $a->getData();

        $recursive_choose = function ($idx_data) use ($choices, &$recursive_choose) {
            if (is_array($idx_data)) {
                return array_map($recursive_choose, $idx_data);
            }
            $idx = (int)$idx_data;
            if ($idx < 0 || $idx >= count($choices)) {
                throw new \Exception("Index {$idx} is out of bounds for choices");
            }
            // This is a simplified version. NumPy's choices can be arrays themselves.
            return $choices[$idx];
        };

        $result = $recursive_choose($a_data);
        return new NDArray($result);
    }
}