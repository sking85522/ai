<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Modf
{
    public static function modf(NDArray $a): array
    {
        $data = $a->getData();
        $frac = [];
        $integ = [];

        $build = function ($x, &$fOut, &$iOut, $build) {
            if (is_array($x)) {
                $fArr = [];
                $iArr = [];
                foreach ($x as $v) {
                    $build($v, $fArr, $iArr, $build);
                }
                $fOut[] = $fArr;
                $iOut[] = $iArr;
                return;
            }
            $i = ($x >= 0) ? floor($x) : ceil($x);
            $fOut[] = $x - $i;
            $iOut[] = $i;
        };

        $build($data, $frac, $integ, $build);
        return [new NDArray($frac), new NDArray($integ)];
    }
}
