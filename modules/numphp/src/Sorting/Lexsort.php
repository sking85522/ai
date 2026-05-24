<?php

namespace NumPHP\Sorting;

use NumPHP\Core\NDArray;

class Lexsort
{
    /**
     * Perform an indirect stable sort using a sequence of keys.
     *
     * @param array|NDArray $keys
     * @return NDArray
     */
    public static function lexsort($keys): NDArray
    {
        $keyData = ($keys instanceof NDArray) ? $keys->getData() : $keys;
        if (!is_array($keyData) || empty($keyData)) {
            throw new \InvalidArgumentException("keys must be a non-empty array of arrays.");
        }

        $first = $keyData[0];
        if (!is_array($first)) {
            throw new \InvalidArgumentException("keys must be a sequence of 1D arrays.");
        }

        $n = count($first);
        foreach ($keyData as $key) {
            if (!is_array($key) || count($key) !== $n) {
                throw new \InvalidArgumentException("all keys must have the same length.");
            }
        }

        $indices = range(0, $n - 1);
        $numKeys = count($keyData);

        usort($indices, function ($i, $j) use ($keyData, $numKeys) {
            for ($k = $numKeys - 1; $k >= 0; $k--) {
                $a = $keyData[$k][$i];
                $b = $keyData[$k][$j];
                if ($a == $b) {
                    continue;
                }
                return ($a < $b) ? -1 : 1;
            }
            return 0;
        });

        return new NDArray($indices, 'int');
    }
}
