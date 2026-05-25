<?php
namespace Core\Commands;

use Core\Tools\Debugger\NeuralDebugger;

class AuditFileCommand implements CommandInterface {
    private NeuralDebugger $debugger;

    public function __construct() {
        $this->debugger = new NeuralDebugger();
    }

    public function canProcess(string $task): bool {
        $task = strtolower($task);
        return str_starts_with($task, 'audit file') || str_starts_with($task, 'audit code') || str_contains($task, 'review file');
    }

    public function process(string $task): string {
        if (preg_match('/(?:file|code|review)\s+([\w\.\/\\\\]+\.\w+)/i', $task, $matches)) {
            $path = $matches[1];
            return $this->debugger->auditFile($path);
        }
        return "[AGENT] Please specify a valid file path to audit. Example: audit file core/Engine/MainEngine.php";
    }
}
