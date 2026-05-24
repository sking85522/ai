<?php

namespace NumPHP\SignalProcessing;

use NumPHP\Core\NDArray;

class FFT
{
    /**
     * Compute the one-dimensional discrete Fourier Transform.
     */
    public static function fft(NDArray $a): NDArray
    {
        $data = \NumPHP\ArrayManipulation\Flatten::flatten($a)->getData();
        $n = count($data);
        if ($n === 0) {
            return new NDArray([]);
        }

        $out = [];
        $twoPi = 2 * M_PI;
        for ($k = 0; $k < $n; $k++) {
            $real = 0.0;
            $imag = 0.0;
            for ($t = 0; $t < $n; $t++) {
                $angle = $twoPi * $k * $t / $n;
                $real += $data[$t] * cos($angle);
                $imag -= $data[$t] * sin($angle);
            }
            $out[] = [$real, $imag];
        }
        return new NDArray($out);
    }
}
