<?php
namespace Core\Tools;

class DebuggerTool implements ToolInterface {
    public function execute(array $input = []): array {
        $task = $input['task'] ?? '';
        $error = trim(str_replace(['debug this error', 'debug', 'fix the bug'], '', $task));

        $debugger = new \Core\Tools\Debugger\NeuralDebugger();
        $response = $debugger->debug($error);

        return ['status' => 'success', 'response' => $response];
    }
}
