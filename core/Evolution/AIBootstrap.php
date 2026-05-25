<?php
namespace Core\Evolution;

use Core\Tools\FileSystem\FileEditor;

/**
 * HRITIK AI - AI BOOTSTRAP
 * Allows the AI to spawn specialized sub-agents to handle specific domains.
 */
class AIBootstrap {
    
    private FileEditor $fileSystem;

    public function __construct() {
        require_once __DIR__ . '/../Tools/FileSystem/FileEditor.php';
        $this->fileSystem = new FileEditor();
    }

    /**
     * Spawns a new sub-agent in a dedicated directory.
     */
    public function spawn(string $name, string $specialization): string {
        $path = "agents/" . strtolower($name);
        
        if (!is_dir(dirname(__DIR__, 2) . "/$path")) {
            mkdir(dirname(__DIR__, 2) . "/$path", 0777, true);
        }

        // Creating the Sub-Agent Config
        $config = [
            'name' => $name . " Agent",
            'parent' => 'Hritik AI',
            'specialization' => $specialization,
            'birth_date' => date('Y-m-d H:i:s')
        ];

        $this->fileSystem->writeFile("$path/agent_manifest.json", json_encode($config, JSON_PRETTY_PRINT));
        
        // Creating a simple logic bridge for the sub-agent
        $logic = "<?php\n// Autonomous Logic for $name ($specialization)\nclass " . $name . "Agent {\n    public function execute(\$task) {\n        return \"Sub-agent '$name' is processing: \" . \$task;\n    }\n}";
        $this->fileSystem->writeFile("$path/Logic.php", $logic);

        return "[BOOTSTRAP] Successfully spawned sub-agent: $name (Specialized in: $specialization)";
    }
}
