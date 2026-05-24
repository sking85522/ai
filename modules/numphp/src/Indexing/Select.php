<?php

namespace NumPHP\Indexing;

use NumPHP\Core\NDArray;

class Select
{
    /**
     * Return an array drawn from elements in choicelist, depending on conditions.
     *
     * @param array $condlist
     * @param array $choicelist
     * @param mixed $default
     * @return NDArray
     */
    public static function select(array $condlist, array $choicelist, $default = 0): NDArray
    {
        if (count($condlist) !== count($choicelist)) {
            throw new \InvalidArgumentException("condlist and choicelist must be of the same length.");
        }

        $result = \NumPHP\Creation\ArrayCreate::full($condlist[0]->getShape(), $default);

        for ($i = 0; $i < count($condlist); $i++) {
            $result = \NumPHP\Indexing\Where::where($condlist[$i], $choicelist[$i], $result);
        }

        return $result;
    }
}