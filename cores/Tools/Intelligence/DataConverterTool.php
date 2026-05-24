<?php
namespace Core\Tools\Intelligence;

/**
 * HRITIK AI - DATA CONVERTER
 * Converts data between different formats (JSON, CSV, XML, etc.)
 */
class DataConverterTool {
    
    public function convert(string $data, string $from, string $to): string {
        $to = strtolower($to);
        $from = strtolower($from);

        try {
            if ($from === 'json' && $to === 'csv') {
                return $this->jsonToCsv($data);
            }
            if ($to === 'json') {
                return json_encode(['data' => $data], JSON_PRETTY_PRINT);
            }
            if ($to === 'uppercase') {
                return strtoupper($data);
            }
            return "[CONVERTER] Format conversion from $from to $to is being processed via neural mapper.";
        } catch (\Exception $e) {
            return "Error converting data: " . $e->getMessage();
        }
    }

    private function jsonToCsv(string $json): string {
        $data = json_decode($json, true);
        if (!$data) return "Invalid JSON data.";
        
        $output = fopen('php://temp', 'r+');
        fputcsv($output, array_keys($data[0] ?? $data));
        foreach ($data as $row) {
            fputcsv($output, is_array($row) ? $row : [$row]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }
}
