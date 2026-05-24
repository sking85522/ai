<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Qr
{
    /**
     * Compute the QR decomposition using Gram-Schmidt.
     *
     * @param NDArray $a
     * @return array{0: NDArray, 1: NDArray}
     */
    public static function qr(NDArray $a): array
    {
        $data = $a->getData();
        if (!is_array($data) || !is_array($data[0] ?? null)) {
            throw new \InvalidArgumentException("qr expects a 2-D array.");
        }

        $m = count($data);
        $n = count($data[0]);
        if ($m === 0 || $n === 0) {
            throw new \InvalidArgumentException("qr expects a non-empty 2-D array.");
        }

        // Build columns
        $Acols = [];
        for ($j = 0; $j < $n; $j++) {
            $col = [];
            for ($i = 0; $i < $m; $i++) {
                $col[] = $data[$i][$j];
            }
            $Acols[] = $col;
        }

        $Qcols = [];
        $R = array_fill(0, $n, array_fill(0, $n, 0.0));

        for ($j = 0; $j < $n; $j++) {
            $v = $Acols[$j];
            for ($i = 0; $i < $j; $i++) {
                $R[$i][$j] = self::dot($Qcols[$i], $Acols[$j]);
                $v = self::sub($v, self::scale($Qcols[$i], $R[$i][$j]));
            }
            $norm = self::norm($v);
            $R[$j][$j] = $norm;
            if ($norm == 0) {
                $Qcols[$j] = array_fill(0, $m, 0.0);
            } else {
                $Qcols[$j] = self::scale($v, 1.0 / $norm);
            }
        }

        // Convert Qcols to matrix Q (m x n)
        $Q = array_fill(0, $m, array_fill(0, $n, 0.0));
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $m; $i++) {
                $Q[$i][$j] = $Qcols[$j][$i];
            }
        }

        return [new NDArray($Q), new NDArray($R)];
    }

    private static function dot(array $a, array $b): float
    {
        $sum = 0.0;
        $n = count($a);
        for ($i = 0; $i < $n; $i++) {
            $sum += $a[$i] * $b[$i];
        }
        return $sum;
    }

    private static function norm(array $a): float
    {
        return sqrt(self::dot($a, $a));
    }

    private static function scale(array $a, float $s): array
    {
        $out = [];
        foreach ($a as $v) {
            $out[] = $v * $s;
        }
        return $out;
    }

    private static function sub(array $a, array $b): array
    {
        $out = [];
        $n = count($a);
        for ($i = 0; $i < $n; $i++) {
            $out[] = $a[$i] - $b[$i];
        }
        return $out;
    }
}
