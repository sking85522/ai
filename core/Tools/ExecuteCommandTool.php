<?php
namespace Core\Tools;

use Core\Tools\Safety\NeuralSafetyGuard;

class ExecuteCommandTool implements ToolInterface {
    private NeuralSafetyGuard $guard;

    public function __construct() {
        $this->guard = new NeuralSafetyGuard();
    }

    public function execute(array $input = []): array {
        $command = trim((string)($input['command'] ?? ''));
        if ($command === '') {
            return ['status' => 'error', 'message' => 'Command is empty.'];
        }

        if (!$this->guard->isCommandSafe($command)) {
            return ['status' => 'error', 'message' => 'Command blocked by safety guard.'];
        }

        return [
            'status' => 'success',
            'payload' => '[CMD_EXEC] ' . $command,
            'command' => $command
        ];
    }
}
