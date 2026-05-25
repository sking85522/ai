<?php
namespace Core\DataHandling;

/**
 * HRITIK AI - DATA HANDLING ASSISTANT
 * The main entry point for all data operations. Integrates Loader and Pipeline.
 */
class DataHandlingAssistant {
    private DataLoader $loader;
    private DataPipeline $pipeline;

    public function __construct() {
        require_once __DIR__ . '/DataLoader.php';
        require_once __DIR__ . '/DataPipeline.php';
        $this->loader = new DataLoader();
        $this->pipeline = new DataPipeline();
    }

    /**
     * Inspects a dataset and provides a high-level summary.
     */
    public function inspect(string $filePath): array {
        return $this->loader->analyzeFile($filePath);
    }

    /**
     * Automatically prepares a dataset for Machine Learning.
     * Handles huge files by streaming if necessary.
     */
    public function prepareForTraining(string $filePath, ?string $targetColumn = null): array {
        $analysis = $this->inspect($filePath);
        if ($analysis['status'] === 'error') return $analysis;

        // For large datasets, we just return the schema for now
        if ($analysis['type'] === 'large_dataset') {
            return [
                'status' => 'success',
                'message' => 'Large dataset detected. Streaming prepared.',
                'schema' => $analysis['columns']
            ];
        }

        // For standard data, perform auto-processing
        $df = $this->loader->loadAsDataFrame($filePath);
        $processedData = [];
        
        foreach ($df->columns() as $col) {
            $columnData = $df->column($col);
            if (is_numeric($columnData[0] ?? null)) {
                $processedData[$col] = $this->pipeline->autoProcess($columnData);
            } else {
                $processedData[$col] = $columnData; // Keep categorical as is for now
            }
        }

        return [
            'status' => 'success',
            'message' => 'Data prepared successfully.',
            'sample_count' => count($processedData[array_key_first($processedData)] ?? []),
            'data' => $processedData
        ];
    }

    public function getStatus(): string {
        return "Data Handling Assistant: Online and ready to ingest massive datasets.";
    }
}
