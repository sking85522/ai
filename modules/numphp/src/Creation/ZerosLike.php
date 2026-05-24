<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class ZerosLike
{
    /**
     * Return an array of zeros with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @return NDArray
     */
    public static function zeros_like(NDArray $prototype): NDArray
    {
        return ArrayCreate::zeros($prototype->getShape(), $prototype->getDtype());
    }
}