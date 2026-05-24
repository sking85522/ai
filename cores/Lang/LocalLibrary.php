<?php
namespace Core\Lang;

/**
 * LocalLibrary
 * Manages the offline translation dictionary and progressive learning.
 */
class LocalLibrary {
    
    private $filePath;
    private $data = [];

    public function __construct() {
        $this->filePath = dirname(__DIR__, 2) . '/storage/data/languages/offline_pack.json';
        $this->load();
    }

    private function load() {
        if (is_file($this->filePath)) {
            $this->data = json_decode(file_get_contents($this->filePath), true) ?: [];
        }
    }

    /**
     * Looks for a phrase in the local dictionary.
     */
    public function find(string $text, string $pair): ?string {
        $text = strtolower(trim($text));
        return $this->data['pack'][$pair][$text] ?? null;
    }

    /**
     * Saves a new translation to the local pack for future offline use.
     */
    public function addPhrase(string $text, string $translation, string $pair) {
        $text = strtolower(trim($text));
        $this->data['pack'][$pair][$text] = $translation;
        file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT));
    }
}
