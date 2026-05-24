<?php

namespace NumPHP\SignalProcessing;

use NumPHP\Core\NDArray;

class Correlate
{
    /**
     * Cross-correlation of two 1-dimensional sequences.
     */
    public static function correlate(NDArray $a, NDArray $v): NDArray
    {
        return Convolve::convolve($a, new NDArray(array_reverse($v->getData())));
    }
}