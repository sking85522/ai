<?php

namespace NumPHP\SignalProcessing;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Roll;

class FFTShift
{
    /**
     * Shift the zero-frequency component to the center of the spectrum.
     */
    public static function fftshift(NDArray $a): NDArray
    {
        $data = $a->getData();
        $n = count($data);
        $shift = (int) ceil($n / 2);

        return Roll::roll($a, $shift);
    }
}