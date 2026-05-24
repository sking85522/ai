<?php
namespace Core\DataHandling\FeatureExtraction;

$datasetDir = dirname(__DIR__, 3) . '/modules/datasetphp';
if (file_exists($datasetDir . '/autoload.php')) {
    require_once $datasetDir . '/autoload.php';
}
if (file_exists($datasetDir . '/DatasetPHP.php')) {
    require_once $datasetDir . '/DatasetPHP.php';
}

use DatasetPHP\DatasetPHP;

class Encoder {

    /**
     * Label Encoding: Converts text labels to unique integers
     * Using the LabelEncoder from the DatasetPHP module
     */
    public function labelEncode(array $labels): array {
        if (!class_exists('DatasetPHP\DatasetPHP')) {
            // Fallback: manual label encoding
            $uniqueInts = array_values(array_unique($labels));
            $map = array_flip($uniqueInts);
            return array_map(fn($l) => $map[$l], $labels);
        }

        $encoder = DatasetPHP::LabelEncoder();
        return method_exists($encoder, 'fitTransform')
            ? $encoder->fitTransform($labels)
            : $encoder->fit_transform($labels);
    }

    /**
     * One-Hot Encoding: Converts labels to a binary matrix
     */
    public function oneHotEncode(array $labels): array {
        if (!class_exists('DatasetPHP\DatasetPHP')) {
            $encoded = $this->labelEncode($labels);
            $classes = count(array_unique($encoded));
            return array_map(function ($label) use ($classes) {
                $row = array_fill(0, $classes, 0);
                if ($label >= 0 && $label < $classes) {
                    $row[$label] = 1;
                }
                return $row;
            }, $encoded);
        }

        $labels = $this->labelEncode($labels);
        $encoder = DatasetPHP::OneHotEncoder();
        return method_exists($encoder, 'fitTransform')
            ? $encoder->fitTransform($labels)
            : $encoder->fit_transform($labels);
    }
}
