<?php
namespace Core\Commands;

use Core\Tools\Debugger\NeuralDebugger;

class DebugCommand implements CommandInterface {
    private NeuralDebugger $debugger;

    public function __construct() {
        $this->debugger = new NeuralDebugger();
    }

    public function canProcess(string $task): bool {
        $task = strtolower($task);
        return str_starts_with($task, 'debug ') || str_starts_with($task, 'fix the bug ') || str_starts_with($task, 'debug error ');
    }

    public function process(string $task): string {
        $error = trim(preg_replace('/^(debug error|debug|fix the bug)\s+/i', '', $task));
        if ($error === '') {
            return "[AGENT] Error message cannot be empty. Use: debug <error_message>";
        }

        return $this->debugger->debug($error);
    }
}
