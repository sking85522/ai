<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;
use NumPHP\Creation\ArrayCreate;

class IsComplex
{
    /**
     * Returns a bool array, where True if input element is complex.
     *
     * @param NDArray $x
     * @return NDArray
     */
    public static function iscomplex(NDArray $x): NDArray
    {
        // In pure PHP, we don't have a native complex type, so this is simplified.
        return ArrayCreate::full($x->getShape(), false, 'bool');
    }
}