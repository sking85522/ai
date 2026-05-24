<?php
namespace Core\Commands;

use Core\Tools\Terminal\ShellExecutor;
use Core\Tools\Safety\NeuralSafetyGuard;

class RunCommandCommand implements CommandInterface {
    private ShellExecutor $terminal;
    private NeuralSafetyGuard $safetyGuard;

    public function __construct() {
        $this->terminal = new ShellExecutor();
        $this->safetyGuard = new NeuralSafetyGuard();
    }

    public function canProcess(string $task): bool {
        $task = strtolower($task);
        return str_starts_with($task, 'run command') || str_starts_with($task, 'execute command');
    }

    public function process(string $task): string {
        $command = trim(preg_replace('/^(run command|execute command)\s+/i', '', $task));
        if ($command === '') {
            return "[AGENT] Command cannot be empty. Use: run command <cmd>";
        }

        if (!$this->safetyGuard->isCommandSafe($command)) {
            return "[AGENT_SAFETY] Blocked: The requested command contains potentially harmful operations.";
        }

        return "[AGENT] Output:\n" . $this->terminal->execute($command);
    }
}
