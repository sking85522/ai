<?php

namespace NumPHP\LinearAlgebra;

use NumPHP\Core\NDArray;

class Svd
{
    /**
     * Compute the singular value decomposition for 2x2 matrices.
     *
     * @param NDArray $a
     * @return array{0: NDArray, 1: NDArray, 2: NDArray}
     */
    public static function svd(NDArray $a): array
    {
        $data = $a->getData();
        if (!is_array($data) || !is_array($data[0] ?? null)) {
            throw new \InvalidArgumentException("svd expects a 2-D array.");
        }

        $rows = count($data);
        $cols = count($data[0]);
        if ($rows !== 2 || $cols !== 2) {
            throw new \Exception("svd only implemented for 2x2 matrices.");
        }

        $a11 = $data[0][0];
        $a12 = $data[0][1];
        $a21 = $data[1][0];
        $a22 = $data[1][1];

        // Compute A^T A
        $ata11 = $a11 * $a11 + $a21 * $a21;
        $ata12 = $a11 * $a12 + $a21 * $a22;
        $ata22 = $a12 * $a12 + $a22 * $a22;

        // Eigen decomposition of A^T A (2x2 symmetric)
        $trace = $ata11 + $ata22;
        $det = $ata11 * $ata22 - $ata12 * $ata12;
        $disc = $trace * $trace - 4 * $det;
        if ($disc < 0) {
            $disc = 0;
        }
        $sqrtDisc = sqrt($disc);
        $lambda1 = ($trace + $sqrtDisc) / 2;
        $lambda2 = ($trace - $sqrtDisc) / 2;

        $sigma1 = sqrt(max(0, $lambda1));
        $sigma2 = sqrt(max(0, $lambda2));

        $v1 = self::eigenvectorSym2x2($ata11, $ata12, $ata22, $lambda1);
        $v2 = self::eigenvectorSym2x2($ata11, $ata12, $ata22, $lambda2);

        // Order by descending singular values
        if ($sigma2 > $sigma1) {
            [$sigma1, $sigma2] = [$sigma2, $sigma1];
            [$v1, $v2] = [$v2, $v1];
        }

        $V = [[$v1[0], $v2[0]], [$v1[1], $v2[1]]];

        // Compute U = A V Sigma^{-1}
        $U = [[0, 0], [0, 0]];
        $sigmas = [$sigma1, $sigma2];
        for ($k = 0; $k < 2; $k++) {
            $sigma = $sigmas[$k];
            if ($sigma == 0) {
                $U[0][$k] = 0;
                $U[1][$k] = 0;
                continue;
            }
            $vk0 = $V[0][$k];
            $vk1 = $V[1][$k];
            $u0 = ($a11 * $vk0 + $a12 * $vk1) / $sigma;
            $u1 = ($a21 * $vk0 + $a22 * $vk1) / $sigma;
            $norm = sqrt($u0 * $u0 + $u1 * $u1);
            if ($norm == 0) {
                $U[0][$k] = 0;
                $U[1][$k] = 0;
            } else {
                $U[0][$k] = $u0 / $norm;
                $U[1][$k] = $u1 / $norm;
            }
        }

        $Uarr = new NDArray($U);
        $Sarr = new NDArray([$sigma1, $sigma2]);
        $Vtarr = new NDArray([[ $V[0][0], $V[1][0] ], [ $V[0][1], $V[1][1] ]]);

        return [$Uarr, $Sarr, $Vtarr];
    }

    private static function eigenvectorSym2x2($a11, $a12, $a22, $lambda): array
    {
        if ($a12 != 0) {
            $v = [$a12, $lambda - $a11];
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
