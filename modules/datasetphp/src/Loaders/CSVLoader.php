<?php

namespace DatasetPHP\Loaders;

class CSVLoader
{
    /**
     * Loads a CSV dataset, splitting features (X) and target (y).
     * @param string $filepath
     * @param string|int $target_column
     * @param string $delimiter
     */
    public static function load(string $filepath, $target_column, string $delimiter = ','): array
    {
        if (!file_exists($filepath)) {
            throw new \Exception("Dataset file not found: $filepath");
        }

        $file = fopen($filepath, 'r');
        $X = [];
        $y = [];
        $header = fgetcsv($file, 0, $delimiter);

        if ($header === false) {
            throw new \Exception("Empty or invalid CSV file: $filepath");
        }

        // Determine target column index
        $target_idx = -1;
        if (is_int($target_column)) {
            $target_idx = $target_column;
        } else {
            $target_idx = array_search($target_column, $header);
            if ($target_idx === false) {
                throw new \InvalidArgumentException("Target column '$target_column' not found in CSV header.");
            }
        }

        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            // Check for empty rows
            if (count($row) == 1 && $row[0] === null) continue;

            // Extract label
            $label = $row[$target_idx];
            $y[] = is_numeric($label) ? (strpos($label, '.') !== false ? (float)$label : (int)$label) : $label;

            // Extract features
            $features = [];
            foreach ($row as $i => $val) {
                if ($i !== $target_idx) {
                    $features[] = is_numeric($val) ? (strpos($val, '.') !== false ? (float)$val : (int)$val) : $val;
                }
            }
            $X[] = $features;
        }

        fclose($file);

        return ['X' => $X, 'y' => $y, 'features' => $header];
    }
}
