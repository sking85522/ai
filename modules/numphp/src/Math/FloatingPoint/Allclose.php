<?php

namespace NumPHP\Math\FloatingPoint;

use NumPHP\Core\NDArray;

class Allclose
{
    public static function allclose(NDArray $a, NDArray $b, float $rtol = 1e-05, float $atol = 1e-08): bool
    {
        if ($a->getShape() !== $b->getShape()) {
            return false;
        }
        $dataA = $a->getData();
        $dataB = $b->getData();

        $check = function ($x, $y) use (&$check, $rtol, $atol) {
            if (is_array($x)) {
                $n = count($x);
                for ($i = 0; $i < $n; $i++) {
                    if (!$check($x[$i], $y[$i])) {
                        return false;
                    }
                }
                return true;
            }
            return abs($x - $y) <= ($atol + $rtol * abs($y));
        };

        return $check($dataA, $dataB);
    }
}
