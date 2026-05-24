<?php
namespace Core\Tools;

use Core\Memory\Storage\OnlineMemoryBridge;

class LearnFactTool implements ToolInterface {
    private OnlineMemoryBridge $memory;

    public function __construct() {
        $this->memory = new OnlineMemoryBridge();
    }

    public function execute(array $input = []): array {
        $fact = trim((string)($input['fact'] ?? ''));
        if ($fact === '') {
            return ['status' => 'error', 'message' => 'Fact is empty.'];
        }

        $key = substr(sha1(strtolower($fact)), 0, 16);
        $saved = $this->memory->save('learned_fact', $key, $fact);

        return [
            'status' => $saved ? 'success' : 'error',
            'fact' => $fact,
            'key' => $key,
            'message' => $saved ? 'Fact saved.' : 'Cloud memory is unavailable.'
        ];
    }
}
