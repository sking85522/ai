<?php
namespace Core\DL\Weights;

/**
 * HRITIK AI - NEURAL WEIGHT MANAGER
 * Handles the persistent storage and retrieval of trained neural weights.
 */
class WeightManager {
    
    private string $storagePath;

    public function __construct(string $path = null) {
        $this->storagePath = $path ?? dirname(__DIR__) . '/Weights/trained_model.json';
    }

    /**
     * Saves neural weights to a JSON file.
     */
    public function save(array $weights): bool {
        $dir = dirname($this->storagePath);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        return (bool)file_put_contents($this->storagePath, json_encode($weights, JSON_PRETTY_PRINT));
    }

    /**
     * Loads neural weights from storage.
     */
    public function load(): ?array {
        if (!file_exists($this->storagePath)) return null;
        return json_decode(file_get_contents($this->storagePath), true);
    }
}
