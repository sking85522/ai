<?php

namespace NumPHP\Random;

use NumPHP\Core\NDArray;
use NumPHP\Core\DType;

class Randn
{
    /**
     * Return a sample (or samples) from the "standard normal" distribution.
     *
     * @param array $shape
     * @return NDArray
     */
    public static function randn(array $shape): NDArray
    {
        $data = self::generate($shape);
        return new NDArray($data, DType::FLOAT);
    }

    private static function generate(array $shape)
    {
        if (empty($shape)) {
            return self::boxMuller();
        }

        $dim = array_shift($shape);
        $result = [];

        for ($i = 0; $i < $dim; $i++) {
            $result[] = self::generate($shape);
        }

        return $result;
    }

    /**
     * Uses Box-Muller transform to generate standard normal distribution
     */
    private static function boxMuller(): float
    {
        $u1 = 0;
        $u2 = 0;
        while ($u1 === 0) $u1 = (float) mt_rand() / mt_getrandmax();
        while ($u2 === 0) $u2 = (float) mt_rand() / mt_getrandmax();

        $z0 = sqrt(-2.0 * log($u1)) * cos(2.0 * M_PI * $u2);
        // $z1 = sqrt(-2.0 * log($u1)) * sin(2.0 * M_PI * $u2); // Unused second value

        return $z0;
    }
}