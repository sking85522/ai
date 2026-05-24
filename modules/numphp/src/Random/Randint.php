<?php

namespace NumPHP\Random;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Randint
{
    public static function randint(int $low, ?int $high = null, $size = null): NDArray
    {
        if ($high === null) {
            $high = $low;
            $low = 0;
        }
        if ($high <= $low) {
            throw new \InvalidArgumentException('high must be greater than low');
        }

        if ($size === null) {
            return new NDArray([mt_rand($low, $high - 1)], 'int');
        }

        $shape = is_array($size) ? $size : [$size];
        $data = Helpers::buildFilled($shape, 0);

        $fill = function ($x) use (&$fill, $low, $high) {
            if (is_array($x)) {
                $out = [];
                foreach ($x as $v) {
                    $out[] = $fill($v);
                }
                return $out;
            }
            return mt_rand($low, $high - 1);
        };

        $data = $fill($data);
        return new NDArray($data, 'int');
    }
}
