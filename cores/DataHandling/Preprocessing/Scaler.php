<?php
namespace Core\DataHandling\Preprocessing;

class Scaler {

    /**
     * Min-Max Scaling: Transforms features to range [0, 1]
     * Formula: x_scaled = (x - x_min) / (x_max - x_min)
     */
    public function minMaxScale(array $data): array {
        if (empty($data)) return $data;
        
        $min = min($data);
        $max = max($data);
        $range = $max - $min;

        if ($range == 0) return array_fill(0, count($data), 0.0);

        return array_map(function($x) use ($min, $range) {
            return ($x - $min) / $range;
        }, $data);
    }

    /**
     * Standard Scaling: Standardize features by removing mean and scaling to unit variance
     * Formula: z = (x - u) / s
     */
    public function standardScale(array $data): array {
        if (empty($data)) return $data;

        $count = count($data);
        $mean = array_sum($data) / $count;

        $variance = 0;
        foreach ($data as $x) {
            $variance += pow($x - $mean, 2);
        }
        $std = sqrt($variance / $count);

        if ($std == 0) return array_fill(0, count($data), 0.0);

        return array_map(function($x) use ($mean, $std) {
            return ($x - $mean) / $std;
        }, $data);
    }
}
