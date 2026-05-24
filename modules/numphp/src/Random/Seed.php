<?php

namespace NumPHP\Random;

use NumPHP\Core\NDArray;

class Seed
{
    /**
     * Seed the random number generator.
     *
     * @param int|null $seed
     * @return void
     */
    public static function seed(?int $seed = null): void
    {
        if ($seed === null) {
            $seed = (int) (microtime(true) * 1000000);
        }
        mt_srand($seed);
    }
}
