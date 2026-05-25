<?php
namespace Core\DataHandling;

/**
 * HRITIK AI - ADVANCED DATA LOADER
 * High-performance data ingestion with streaming support for massive files.
 */
class DataLoader {
    
    private bool $pandaLoaded = false;
    private int $chunkSize = 5000; // Rows per chunk

    public function __construct() {
        if (file_exists(dirname(__DIR__, 2) . '/modules/pandaphp/autoload.php')) {
            require_once dirname(__DIR__, 2) . '/modules/pandaphp/autoload.php';
        }
        $this->pandaLoaded = class_exists('PandaPHP\PandaPHP');
    }

    /**
     * Streams a large CSV file without loading it entirely into memory.
     */
    public function streamCSV(string $filePath, callable $onChunk): void {
        if (!file_exists($filePath)) return;

        $handle = fopen($filePath, "r");
        $headers = fgetcsv($handle);
        $chunk = [];
        
        while (($row = fgetcsv($handle)) !== false) {
            $chunk[] = array_combine($headers, $row);
            if (count($chunk) >= $this->chunkSize) {
                $onChunk($chunk);
                $chunk = [];
            }
        }
        
        if (!empty($chunk)) $onChunk($chunk);
        fclose($handle);
    }

    /**
     * Loads a file as a DataFrame (PandaPHP) for small/medium files.
     */
    public function loadAsDataFrame(string $filePath) {
        if (!$this->pandaLoaded) throw new \Exception("PandaPHP module not installed.");
        
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext === 'json') {
            $content = file_get_contents($filePath);
            $data = json_decode($content, true);
            return \PandaPHP\PandaPHP::fromArray($data);
        }
        
        return \PandaPHP\PandaPHP::read_csv($filePath);
    }

    /**
     * Professional file analysis with memory protection.
     */
    public function analyzeFile(string $filePath): array {
        if (!file_exists($filePath)) return ['status' => 'error', 'message' => 'File not found.'];
        
        $size = filesize($filePath);
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Use streaming if file is > 10MB to avoid memory leaks
        if ($size > 10 * 1024 * 1024) {
            $handle = fopen($filePath, "r");
            $headers = ($ext === 'csv') ? fgetcsv($handle) : ['json_data'];
            fclose($handle);
            
            return [
                'status' => 'success',
                'type' => 'large_dataset',
                'columns' => $headers,
                'size_mb' => round($size / (1024 * 1024), 2),
                'note' => 'Streaming mode recommended'
            ];
        }

        try {
            $df = $this->loadAsDataFrame($filePath);
            $shape = $df->shape();
            return [
                'status' => 'success',
                'type' => 'standard_dataframe',
                'columns' => $df->columns(),
                'rows' => $shape[0],
                'cols' => $shape[1],
                'size_mb' => round($size / (1024 * 1024), 2)
            ];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
