<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class OnesLike
{
    /**
     * Return an array of ones with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @return NDArray
     */
    public static function ones_like(NDArray $prototype): NDArray
    {
        return ArrayCreate::ones($prototype->getShape(), $prototype->getDtype());
    }
}