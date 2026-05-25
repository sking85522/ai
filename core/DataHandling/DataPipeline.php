<?php
namespace Core\DataHandling;

/**
 * HRITIK AI - NEURAL DATA PIPELINE
 * Professional data preprocessing, cleaning, and feature engineering using Matrix engines.
 */
class DataPipeline {
    
    public function __construct() {
        require_once dirname(__DIR__) . '/Matrix/MatrixOps.php';
    }

    /**
     * Standardizes a numeric column using Z-Score (Mu=0, Std=1)
     */
    public function standardize(array $data): array {
        if (empty($data)) return [];
        
        $mean = \Core\Matrix\MatrixOps::mean($data);
        $std = \Core\Matrix\MatrixOps::std($data);
        
        return array_map(fn($v) => ($v - $mean) / ($std ?: 1), $data);
    }

    /**
     * Normalizes a numeric column to a range [0, 1]
     */
    public function normalize(array $data): array {
        if (empty($data)) return [];
        
        $min = \Core\Matrix\MatrixOps::min($data);
        $max = \Core\Matrix\MatrixOps::max($data);
        $range = $max - $min;
        
        return array_map(fn($v) => ($v - $min) / ($range ?: 1), $data);
    }

    /**
     * Outlier Removal using Interquartile Range (IQR) method
     */
    public function cleanOutliers(array $data): array {
        if (count($data) < 4) return $data;
        
        sort($data);
        $q1 = $data[floor(count($data) * 0.25)];
        $q3 = $data[floor(count($data) * 0.75)];
        $iqr = $q3 - $q1;
        
        $lowerBound = $q1 - 1.5 * $iqr;
        $upperBound = $q3 + 1.5 * $iqr;
        
        return array_filter($data, fn($v) => $v >= $lowerBound && $v <= $upperBound);
    }

    /**
     * Automated Preprocessing: Clean, Impute, and Normalize
     */
    public function autoProcess(array $data): array {
        // 1. Remove Outliers
        $data = $this->cleanOutliers($data);
        
        // 2. Impute (Fill zeros for missing values - Simple heuristic)
        $data = array_map(fn($v) => $v === null ? 0 : $v, $data);
        
        // 3. Normalize
        return $this->normalize($data);
    }
}
