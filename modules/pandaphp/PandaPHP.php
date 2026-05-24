<?php

namespace PandaPHP;

use PandaPHP\Core\DataFrame;
use PandaPHP\Core\Series;

class PandaPHP
{
    // ──────────── Creation ────────────

    public static function DataFrame($data = [], $index = null, $columns = null): DataFrame
    {
        return new DataFrame($data, $index, $columns);
    }

    public static function Series($data, $index = null, string $name = ''): Series
    {
        return new Series($data, $index, $name);
    }

    // ──────────── CSV I/O ────────────

    public static function read_csv(string $filepath, array $options = []): DataFrame
    {
        $delimiter = $options['delimiter'] ?? ',';
        $header = $options['header'] ?? 0;

        if (!file_exists($filepath)) {
            throw new \Exception("File not found: $filepath");
        }

        $file = fopen($filepath, 'r');
        $data = [];
        $columns = null;
        $row_idx = 0;

        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            if ($header !== null && $row_idx === $header) {
                $columns = $row;
            } else {
                $parsed_row = array_map(function($val) {
                    if (is_numeric($val)) {
                        return strpos($val, '.') !== false ? (float)$val : (int)$val;
                    }
                    return $val;
                }, $row);
                $data[] = $parsed_row;
            }
            $row_idx++;
        }
        fclose($file);

        return new DataFrame($data, null, $columns);
    }

    /**
     * Write DataFrame to CSV using public API (no Reflection hack).
     */
    public static function to_csv(DataFrame $df, string $filepath, array $options = []): void
    {
        $delimiter = $options['delimiter'] ?? ',';
        $includeIndex = $options['index'] ?? true;

        $file = fopen($filepath, 'w');

        // Get columns and data through public methods
        $columns = $df->columns();
        $shape = $df->shape();

        // Write header
        $header = $columns;
        if ($includeIndex) {
            array_unshift($header, '');
        }
        fputcsv($file, $header, $delimiter);

        // Write rows
        for ($i = 0; $i < $shape[0]; $i++) {
            $row = [];
            if ($includeIndex) {
                $idx = $df->getIndex();
                $row[] = $idx[$i] ?? $i;
            }
            foreach ($columns as $col) {
                $row[] = $df->get($col, $i);
            }
            fputcsv($file, $row, $delimiter);
        }

        fclose($file);
    }

    // ──────────── JSON I/O ────────────

    public static function read_json(string $filepath): DataFrame
    {
        $json = file_get_contents($filepath);
        $data = json_decode($json, true);
        if (!$data) throw new \Exception("Invalid JSON file.");

        $columns = array_keys($data[0] ?? []);
        $rows = [];
        foreach ($data as $record) {
            $rows[] = array_values($record);
        }
        return new DataFrame($rows, null, $columns);
    }

    public static function to_json(DataFrame $df, string $filepath): void
    {
        $columns = $df->columns();
        $shape = $df->shape();
        $records = [];
        for ($i = 0; $i < $shape[0]; $i++) {
            $record = [];
            foreach ($columns as $col) {
                $record[$col] = $df->get($col, $i);
            }
            $records[] = $record;
        }
        file_put_contents($filepath, json_encode($records, JSON_PRETTY_PRINT));
    }
}
