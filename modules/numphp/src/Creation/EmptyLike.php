<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class EmptyLike
{
    /**
     * Return a new array with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @return NDArray
     */
    public static function empty_like(NDArray $prototype): NDArray
    {
        return ArrayCreate::full($prototype->getShape(), null, $prototype->getDtype());
    }
}