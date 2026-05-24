<?php

namespace NumPHP\Polynomial;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Polyval
{
    /**
     * Evaluate a polynomial at specific values.
     *
     * @param NDArray|array $p 1D array of polynomial coefficients (including coefficients equal to zero) from highest degree to the constant term.
     * @param NDArray|float|int $x A number or array of numbers at which to evaluate p.
     * @return NDArray
     */
    public static function polyval($p, $x): NDArray
    {
        $coeffs = ($p instanceof NDArray) ? $p->getData() : $p;
        if (!is_array($coeffs)) $coeffs = [$coeffs];

        // Horner's method
        $func = function($val) use ($coeffs) {
            $res = 0;
            foreach ($coeffs as $c) {
                $res = $res * $val + $c;
            }
            return $res;
        };

        if ($x instanceof NDArray) {
            $xData = $x->getData();
            $recursiveMap = function($d) use ($func, &$recursiveMap) {
                return is_array($d) ? array_map($recursiveMap, $d) : $func($d);
            };
            return new NDArray($recursiveMap($xData), 'float');
        } else {
            return new NDArray($func($x), 'float');
        }
    }
}
