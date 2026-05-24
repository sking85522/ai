<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Histogramdd
{
    public static function histogramdd($sample, int $bins = 10): array
    {
        // Minimal 2D support
        if ($sample instanceof NDArray) {
            $data = $sample->getData();
            if (is_array($data) && isset($data[0]) && is_array($data[0]) && count($data[0]) === 2) {
                $x = new NDArray(array_column($data, 0));
                $y = new NDArray(array_column($data, 1));
                return Histogram2D::histogram2d($x, $y, $bins);
            }
        }
        if (is_array($sample) && count($sample) === 2) {
            return Histogram2D::histogram2d($sample[0], $sample[1], $bins);
        }
        throw new \Exception('histogramdd only supports 2D samples in this implementation');
    }
}
