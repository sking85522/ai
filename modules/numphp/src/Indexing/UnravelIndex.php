<?php

namespace NumPHP\Indexing;

class UnravelIndex
{
    public static function unravel_index(int $index, array $shape): array
    {
        $coords = [];
        $n = count($shape);
        for ($i = $n - 1; $i >= 0; $i--) {
            $dim = $shape[$i];
            $coords[$i] = $index % $dim;
            $index = intdiv($index, $dim);
        }
        ksort($coords);
        return array_values($coords);
    }
}
