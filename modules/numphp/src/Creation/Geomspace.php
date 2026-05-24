<?php

namespace NumPHP\Creation;

use NumPHP\Core\NDArray;

class Geomspace
{
    /**
     * Return numbers spaced evenly on a log scale (a geometric progression).
     *
     * @param float $start
     * @param float $stop
     * @param int $num
     * @param bool $endpoint
     * @param string|null $dtype
     * @return NDArray
     */
    public static function geomspace(float $start, float $stop, int $num = 50, bool $endpoint = true, ?string $dtype = null): NDArray
    {
        if ($start <= 0 || $stop <= 0) {
            throw new \InvalidArgumentException("Geomspace endpoints must be positive.");
        }

        $log_start = log($start);
        $log_stop = log($stop);

        $step = ($num > 1 && $endpoint) ? (($log_stop - $log_start) / ($num - 1)) : (($log_stop - $log_start) / $num);

        $data = [];
        for ($i = 0; $i < $num; $i++) {
            $data[] = exp($log_start + ($i * $step));
        }
        return new NDArray($data, $dtype);
    }
}