<?php
namespace Core\DataHandling\Preprocessing;

class Imputer {

    /**
     * Impute missing values (represented by null or NaN)
     * Strategy: mean, median, or constant
     */
    public function impute(array $data, string $strategy = 'mean', $constant = 0): array {
        $cleanData = array_filter($data, function($x) {
            return $x !== null && !is_nan($x);
        });

        if (empty($cleanData)) return array_fill(0, count($data), $constant);

        $replacement = $constant;
        if ($strategy === 'mean') {
            $replacement = array_sum($cleanData) / count($cleanData);
        } elseif ($strategy === 'median') {
            sort($cleanData);
            $mid = floor(count($cleanData) / 2);
            $replacement = $cleanData[$mid];
        }

        return array_map(function($x) use ($replacement) {
            return ($x === null || is_nan($x)) ? $replacement : $x;
        }, $data);
    }
}
