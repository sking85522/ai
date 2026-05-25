<?php
namespace Core\Virtual;

/**
 * HRITIK AI - MENTAL MODEL
 * Maintains a persistent state of the AI's current goals and projects.
 */
class MentalModel {
    
    private string $storageFile;
    private array $state = [];

    public function __construct() {
        $dir = dirname(__DIR__, 2) . '/localstorage/data';
        if (!is_dir($dir)) @mkdir($dir, 0777, true);
        $this->storageFile = $dir . '/mental_state.json';
        $this->load();
    }

    private function load() {
        if (is_file($this->storageFile)) {
            $this->state = json_decode(file_get_contents($this->storageFile), true) ?: [];
        } else {
            $this->state = ['current_project' => null, 'last_action' => null, 'goal_stack' => []];
        }
    }

    public function update(string $key, $value) {
        $this->state[$key] = $value;
        $this->save();
    }

    public function getState(string $key) {
        return $this->state[$key] ?? null;
    }

    private function save() {
        file_put_contents($this->storageFile, json_encode($this->state, JSON_PRETTY_PRINT));
    }
}
