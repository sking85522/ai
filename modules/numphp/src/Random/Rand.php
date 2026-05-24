<?php

namespace NumPHP\Random;

use NumPHP\Core\NDArray;
use NumPHP\Core\DType;

class Rand
{
    /**
     * Create an array of the given shape and populate it with
     * random samples from a uniform distribution over [0, 1).
     *
     * @param array $shape
     * @return NDArray
     */
    public static function rand(array $shape): NDArray
    {
        $data = self::generate($shape);
        return new NDArray($data, DType::FLOAT);
    }

    private static function generate(array $shape)
    {
        if (empty($shape)) {
            return (float) mt_rand() / mt_getrandmax();
        }

        $dim = array_shift($shape);
        $result = [];

        for ($i = 0; $i < $dim; $i++) {
            $result[] = self::generate($shape);
        }

        return $result;
    }
}