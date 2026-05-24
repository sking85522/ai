<?php

namespace SciPHP\Signal;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class Convolve
{
    /**
     * Convolve two N-dimensional arrays.
     * Currently only supports 1D arrays, approximates scipy.signal.convolve.
     *
     * @param NDArray|array $in1 First input.
     * @param NDArray|array $in2 Second input. Should have the same number of dimensions as in1.
     * @param string $mode A string indicating the size of the output: 'full', 'valid', 'same' (default 'full').
     * @return NDArray
     */
    public static function convolve1d($in1, $in2, string $mode = 'full'): NDArray
    {
        $a = ($in1 instanceof NDArray) ? $in1->getData() : $in1;
        $v = ($in2 instanceof NDArray) ? $in2->getData() : $in2;

        $len_a = count($a);
        $len_v = count($v);

        if ($len_a == 0 || $len_v == 0) {
            return np::array([]);
        }

        $out_len = $len_a + $len_v - 1;
        $out = array_fill(0, $out_len, 0);

        // Basic discrete linear convolution
        for ($i = 0; $i < $len_a; $i++) {
            for ($j = 0; $j < $len_v; $j++) {
                $out[$i + $j] += $a[$i] * $v[$j];
            }
        }

        if ($mode === 'full') {
            return np::array($out);
        } elseif ($mode === 'same') {
            $start = (int)(($len_v - 1) / 2);
            $sliced = array_slice($out, $start, $len_a);
            return np::array($sliced);
        } elseif ($mode === 'valid') {
            $max_len = max($len_a, $len_v);
            $min_len = min($len_a, $len_v);
            $start = $min_len - 1;
            $sliced = array_slice($out, $start, $max_len - $min_len + 1);
            return np::array($sliced);
        }

        throw new \InvalidArgumentException("Invalid mode: $mode");
    }
}
