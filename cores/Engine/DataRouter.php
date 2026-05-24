<?php
namespace Core\Engine;

/**
 * HRITIK AI - DATA ROUTER
 * Handles Visual Analysis and Data Inspections.
 */
class DataRouter {
    private $dataAi;

    public function __construct($dataAi) {
        $this->dataAi = $dataAi;
    }

    public function handleFile(?string $datasetFile, ?string $originalName): ?array {
        if (!$datasetFile) return null;

        $ext = strtolower(pathinfo($originalName ?? $datasetFile, PATHINFO_EXTENSION));
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $imageExts)) {
            require_once __DIR__ . '/../Tools/Vision/NeuralEyeCore.php';
            $cv = new \Core\Tools\Vision\NeuralEyeCore();
            return [
                'response' => $cv->analyzeImage($datasetFile),
                'intent' => 'vision'
            ];
        }

        return [
            'response' => $this->dataAi->inspect($datasetFile),
            'intent' => 'data_analysis'
        ];
    }
}
