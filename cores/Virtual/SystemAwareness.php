<?php
namespace Core\Virtual;

/**
 * HRITIK AI - SYSTEM AWARENESS
 * Gives the AI full knowledge of its own core modules, file structure, and capabilities.
 */
class SystemAwareness {
    
    private string $root;

    public function __construct() {
        $this->root = dirname(__DIR__, 2);
    }

    /**
     * Returns a full index of all active modules and their purposes.
     */
    public function getModuleManifest(): array {
        return [
            'SciPHP' => [
                'path' => 'modules/sciphp',
                'purpose' => 'Advanced scientific computing, optimization, and linear algebra.',
                'files' => count(glob($this->root . '/modules/sciphp/src/*.php')),
                'functions' => 'solve, minimize, interpolate, integrate'
            ],
            'NumPHP' => [
                'path' => 'modules/numphp',
                'purpose' => 'High-performance n-dimensional array processing (Matrix math).',
                'files' => count(glob($this->root . '/modules/numphp/src/*.php')),
                'functions' => 'dot, sum, mean, reshape, transpose'
            ],
            'AgenticCore' => [
                'path' => 'core/Engine/AgenticCore.php',
                'purpose' => 'Autonomous task execution (File editing, terminal access).',
                'capabilities' => 'Code generation, debugging, project planning'
            ]
        ];
    }

    /**
     * Summarizes what the AI knows about itself.
     */
    public function selfIdentify(): string {
        $manifest = $this->getModuleManifest();
        $summary = "Main Hritik AI Engine hoon. Mere paas " . count($manifest) . " bade modules hain:\n";
        foreach ($manifest as $name => $info) {
            $summary .= "- $name: {$info['purpose']}\n";
        }
        return $summary;
    }
}
