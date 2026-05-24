<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Resize
{
    public static function resize(NDArray $a, array $new_shape): NDArray
    {
        $flat = [];
        Helpers::flatten($a->getData(), $flat);
        if (empty($flat)) {
            return new NDArray(Helpers::buildFilled($new_shape, null));
        }
        $size = array_product($new_shape);
        $outFlat = [];
        for ($i = 0; $i < $size; $i++) {
            $outFlat[] = $flat[$i % count($flat)];
        }
        $idx = 0;
        $build = function ($shape) use (&$build, &$outFlat, &$idx) {
            if (empty($shape)) {
                $v = $outFlat[$idx];
                $idx++;
                return $v;
            }
            $dim = array_shift($shape);
            $out = [];
            for ($i = 0; $i < $dim; $i++) {
                $out[] = $build($shape);
            }
            return $out;
        };
        return new NDArray($build($new_shape));
    }
}
