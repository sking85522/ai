<?php

namespace NumPHP\SignalProcessing;

use NumPHP\Core\NDArray;

class IFFT
{
    /**
     * Compute the one-dimensional inverse discrete Fourier Transform.
     */
    public static function ifft(NDArray $a): NDArray
    {
        // A full FFT implementation is very complex.
        throw new \Exception("ifft is a placeholder and not fully implemented in pure PHP.");
    }
}