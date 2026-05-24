<?php

namespace NumPHP\Types;

use NumPHP\Core\NDArray;
use NumPHP\Creation\ArrayCreate;

class IsReal
{
    /**
     * Returns a bool array, where True if input element is real.
     *
     * @param NDArray $x
     * @return NDArray
     */
    public static function isreal(NDArray $x): NDArray
    {
        // In pure PHP, all standard numbers are "real".
        return ArrayCreate::full($x->getShape(), true, 'bool');
    }
}