<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;
use NumPHP\ArrayManipulation\Flatten;

class Histogram2D
{
    public static function histogram2d(NDArray $x, NDArray $y, int $bins = 10): array
    {
        $dx = Flatten::flatten($x)->getData();
        $dy = Flatten::flatten($y)->getData();
        $n = min(count($dx), count($dy));
        $dx = array_slice($dx, 0, $n);
        $dy = array_slice($dy, 0, $n);

        if ($n === 0) {
            return [new NDArray(array_fill(0, $bins, array_fill(0, $bins, 0))), new NDArray(range(0, $bins)), new NDArray(range(0, $bins))];
        }

        $xmin = min($dx); $xmax = max($dx);
        $ymin = min($dy); $ymax = max($dy);
        if ($xmin === $xmax) { $xmin -= 0.5; $xmax += 0.5; }
        if ($ymin === $ymax) { $ymin -= 0.5; $ymax += 0.5; }
        $xw = ($xmax - $xmin) / $bins;
        $yw = ($ymax - $ymin) / $bins;
        $xedges = array_map(function ($i) use ($xmin, $xw) { return $xmin + $i * $xw; }, range(0, $bins));
        $yedges = array_map(function ($i) use ($ymin, $yw) { return $ymin + $i * $yw; }, range(0, $bins));

        $hist = array_fill(0, $bins, array_fill(0, $bins, 0));
        for ($i = 0; $i < $n; $i++) {
            $xi = ($dx[$i] == $xmax) ? ($bins - 1) : (int) floor(($dx[$i] - $xmin) / $xw);
            $yi = ($dy[$i] == $ymax) ? ($bins - 1) : (int) floor(($dy[$i] - $ymin) / $yw);
            if ($xi >= 0 && $xi < $bins && $yi >= 0 && $yi < $bins) {
                $hist[$xi][$yi]++;
            }
        }

        return [new NDArray($hist), new NDArray($xedges), new NDArray($yedges)];
    }
}
