<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Meshgrid
{
    public static function meshgrid(array $x, array $y): array
    {
        $rows = count($y);
        $cols = count($x);
        $X = [];
        $Y = [];
        for ($i = 0; $i < $rows; $i++) {
            $rowX = [];
            $rowY = [];
            for ($j = 0; $j < $cols; $j++) {
                $rowX[] = $x[$j];
                $rowY[] = $y[$i];
            }
            $X[] = $rowX;
            $Y[] = $rowY;
        }
        return [new NDArray($X), new NDArray($Y)];
    }
}
