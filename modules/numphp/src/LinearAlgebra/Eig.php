<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Eig
{
    /**
     * Compute eigenvalues and right eigenvectors for 2x2 matrices.
     *
     * @param NDArray $a
     * @return array{0: NDArray, 1: NDArray}
     */
    public static function eig(NDArray $a): array
    {
        $data = $a->getData();
        if (!is_array($data) || !is_array($data[0] ?? null)) {
            throw new \InvalidArgumentException("eig expects a 2-D array.");
        }

        $rows = count($data);
        $cols = count($data[0]);
        if ($rows !== $cols) {
            throw new \InvalidArgumentException("eig requires a square matrix.");
        }

        if ($rows === 1) {
            return [new NDArray([$data[0][0]]), new NDArray([[1]])];
        }

        if ($rows !== 2) {
            throw new \Exception("eig only implemented for 1x1 and 2x2 matrices.");
        }

        $a11 = $data[0][0];
        $a12 = $data[0][1];
        $a21 = $data[1][0];
        $a22 = $data[1][1];

        $trace = $a11 + $a22;
        $det = ($a11 * $a22) - ($a12 * $a21);
        $disc = $trace * $trace - 4 * $det;
        if ($disc < 0) {
            $disc = 0;
        }
        $sqrtDisc = sqrt($disc);
        $lambda1 = ($trace + $sqrtDisc) / 2;
        $lambda2 = ($trace - $sqrtDisc) / 2;

        $v1 = self::eigenvector2x2($a11, $a12, $a21, $a22, $lambda1);
        $v2 = self::eigenvector2x2($a11, $a12, $a21, $a22, $lambda2);

        $values = new NDArray([$lambda1, $lambda2]);
        $vectors = new NDArray([[$v1[0], $v2[0]], [$v1[1], $v2[1]]]);
        return [$values, $vectors];
    }

    private static function eigenvector2x2($a11, $a12, $a21, $a22, $lambda): array
    {
        if ($a12 != 0) {
            $v = [$a12, $lambda - $a11];
        } elseif ($a21 != 0) {
            $v = [$lambda - $a22, $a21];
        } else {
            $v = [1, 0];
        }

        $norm = sqrt($v[0] * $v[0] + $v[1] * $v[1]);
        if ($norm == 0) {
            return [1, 0];
        }
        return [$v[0] / $norm, $v[1] / $norm];
    }
}
