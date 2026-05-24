<?php

namespace NumPHP\Polynomial;

use NumPHP\Core\NDArray;

class Roots
{
    /**
     * Return the roots of a polynomial with the given coefficients.
     *
     * @param NDArray|array $p
     * @return NDArray
     */
    public static function roots($p): NDArray
    {
        $coeffs = ($p instanceof NDArray) ? \NumPHP\ArrayManipulation\Flatten::flatten($p)->getData() : $p;
        $deg = count($coeffs) - 1;

        if ($deg < 1) {
            return new NDArray([]);
        }

        if ($deg === 1) { // ax + b
            $a = $coeffs[0];
            $b = $coeffs[1];
            return new NDArray([-$b / $a]);
        }

        if ($deg === 2) { // ax^2 + bx + c
            $a = $coeffs[0];
            $b = $coeffs[1];
            $c = $coeffs[2];
            $discriminant = $b * $b - 4 * $a * $c;

            $r1 = (-$b + sqrt($discriminant)) / (2 * $a);
            $r2 = (-$b - sqrt($discriminant)) / (2 * $a);
            return new NDArray([$r1, $r2], 'float');
        }

        throw new \Exception("Roots only implemented for degree 1 and 2 polynomials in this version.");
    }
}
