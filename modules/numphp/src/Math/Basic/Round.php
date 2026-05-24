<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;

class Round
{
    public static function round(NDArray $a, int $decimals = 0): NDArray
    {
        $data = $a->getData();
        $result = self::recursiveRound($data, $decimals);
        // Rounding often results in floats, but if decimals=0 it might be int conceptually.
        // Keeping original dtype or FLOAT usually safe.
        return new NDArray($result, $a->getDtype());
    }

    private static function recursiveRound($data, int $decimals)
    {
        if (is_array($data)) {
            $func = function ($val) use ($decimals) {
                return self::recursiveRound($val, $decimals);
            };
            return array_map($func, $data);
        }

        return round($data, $decimals);
    }
}