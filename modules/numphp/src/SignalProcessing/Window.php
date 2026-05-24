<?php

namespace NumPHP\SignalProcessing;

use NumPHP\Core\NDArray;
use NumPHP\Creation\Linspace;

class Window
{
    public static function hamming(int $M): NDArray
    {
        if ($M < 1) {
            return new NDArray([]);
        }
        if ($M == 1) {
            return new NDArray([1.0]);
        }
        $n = Linspace::linspace(0, $M - 1, $M)->getData();
        $w = array_map(function($val) use ($M) {
            return 0.54 - 0.46 * cos(2.0 * M_PI * $val / ($M - 1));
        }, $n);
        return new NDArray($w);
    }

    public static function hanning(int $M): NDArray
    {
        if ($M < 1) {
            return new NDArray([]);
        }
        if ($M == 1) {
            return new NDArray([1.0]);
        }
        $n = Linspace::linspace(0, $M - 1, $M)->getData();
        $w = array_map(function($val) use ($M) {
            return 0.5 * (1 - cos(2.0 * M_PI * $val / ($M - 1)));
        }, $n);
        return new NDArray($w);
    }

    public static function blackman(int $M): NDArray
    {
        // Simplified Blackman
        if ($M < 1) return new NDArray([]);
        if ($M == 1) return new NDArray([1.0]);
        $n = Linspace::linspace(0, $M - 1, $M)->getData();
        $w = array_map(function($val) use ($M) {
            return 0.42 - 0.5 * cos(2 * M_PI * $val / ($M - 1)) + 0.08 * cos(4 * M_PI * $val / ($M - 1));
        }, $n);
        return new NDArray($w);
    }

    public static function bartlett(int $M): NDArray
    {
        if ($M < 1) return new NDArray([]);
        if ($M == 1) return new NDArray([1.0]);
        $n = Linspace::linspace(0, $M - 1, $M)->getData();
        $w = array_map(function($val) use ($M) {
            return 1.0 - abs(2 * $val / ($M - 1) - 1);
        }, $n);
        return new NDArray($w);
    }

    public static function kaiser(int $M, float $beta = 14.0): NDArray
    {
        if ($M < 1) return new NDArray([]);
        if ($M == 1) return new NDArray([1.0]);
        $den = self::i0($beta);
        $n = Linspace::linspace(0, $M - 1, $M)->getData();
        $w = array_map(function($val) use ($M, $beta, $den) {
            $t = (2 * $val) / ($M - 1) - 1;
            $arg = $beta * sqrt(1 - $t * $t);
            return self::i0($arg) / $den;
        }, $n);
        return new NDArray($w);
    }

    private static function i0(float $x): float
    {
        $sum = 1.0;
        $y = ($x * $x) / 4.0;
        $t = 1.0;
        for ($k = 1; $k <= 10; $k++) {
            $t *= $y / ($k * $k);
            $sum += $t;
        }
        return $sum;
    }
}
