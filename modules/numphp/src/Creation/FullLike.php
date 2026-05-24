<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class FullLike
{
    /**
     * Return a full array with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @param mixed $fill_value
     * @return NDArray
     */
    public static function full_like(NDArray $prototype, $fill_value): NDArray
    {
        return ArrayCreate::full($prototype->getShape(), $fill_value, $prototype->getDtype());
    }
}