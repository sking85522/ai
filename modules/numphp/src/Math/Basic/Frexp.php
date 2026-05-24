<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Frexp
{
    public static function frexp(NDArray $a): array
    {
        $mantissas = [];
        $exps = [];
        $data = $a->getData();

        $build = function ($x, &$mOut, &$eOut, $build) {
            if (is_array($x)) {
                $mArr = [];
                $eArr = [];
                foreach ($x as $v) {
                    $build($v, $mArr, $eArr, $build);
                }
                $mOut[] = $mArr;
                $eOut[] = $eArr;
                return;
            }
            if ($x == 0.0) {
                $mOut[] = 0.0;
                $eOut[] = 0;
                return;
            }
            $e = (int) floor(log(abs($x), 2)) + 1;
            $m = $x / pow(2, $e);
            $mOut[] = $m;
            $eOut[] = $e;
        };

        $build($data, $mantissas, $exps, $build);
        return [new NDArray($mantissas), new NDArray($exps, 'int')];
    }
}
