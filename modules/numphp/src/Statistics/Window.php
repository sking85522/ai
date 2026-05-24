<?php

namespace NumPHP\SignalProcessing;

use NumPHP\Core\NDArray;

class Window
{
    /**
     * Return the Hamming window.
     *
     * @param int $M Number of points in the output window.
     * @return NDArray
     */
    public static function hamming(int $M): NDArray
    {
        if ($M < 1) return new NDArray([]);
        if ($M == 1) return new NDArray([1.0]);

        $res = [];
        for ($n = 0; $n < $M; $n++) {
            $res[] = 0.54 - 0.46 * cos(2 * M_PI * $n / ($M - 1));
        }
        return new NDArray($res);
    }

    /**
     * Return the Hanning window.
     *
     * @param int $M Number of points in the output window.
     * @return NDArray
     */
    public static function hanning(int $M): NDArray
    {
        if ($M < 1) return new NDArray([]);
        if ($M == 1) return new NDArray([1.0]);

        $res = [];
        for ($n = 0; $n < $M; $n++) {
            $res[] = 0.5 - 0.5 * cos(2 * M_PI * $n / ($M - 1));
        }
        return new NDArray($res);
    }

    /**
     * Return the Blackman window.
     *
     * @param int $M Number of points in the output window.
     * @return NDArray
     */
    public static function blackman(int $M): NDArray
    {
        if ($M < 1) return new NDArray([]);
        if ($M == 1) return new NDArray([1.0]);

        $res = [];
        for ($n = 0; $n < $M; $n++) {
            $res[] = 0.42 - 0.5 * cos(2 * M_PI * $n / ($M - 1)) + 0.08 * cos(4 * M_PI * $n / ($M - 1));
        }
        return new NDArray($res);
    }

    /**
     * Return the Bartlett window.
     *
     * @param int $M Number of points in the output window.
     * @return NDArray
     */
    public static function bartlett(int $M): NDArray
    {
        if ($M < 1) return new NDArray([]);
        if ($M == 1) return new NDArray([1.0]);

        $res = [];
        for ($n = 0; $n < $M; $n++) {
            $res[] = 1.0 - abs(2 * $n / ($M - 1) - 1.0);
        }
        return new NDArray($res);
    }
}