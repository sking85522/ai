<?php
namespace Core\Engine;

require_once __DIR__ . '/../Tools/Intelligence/CoderTool.php';
use Core\Tools\Intelligence\CoderTool;

class ToolOrchestrator {
    private CoderTool $coder;

    public function __construct($toolManager) {
        $this->coder = new CoderTool();
    }

    public function resolve(string $prompt): ?array {
        $prompt = strtolower($prompt);

        // 1. Math Tool (New!)
        if (preg_match('/(\d+)\s*([\+\-\*\/\^])\s*(\d+)/', $prompt, $matches)) {
            $n1 = (float)$matches[1];
            $op = $matches[2];
            $n2 = (float)$matches[3];
            $res = 0;
            switch($op) {
                case '+': $res = $n1 + $n2; break;
                case '-': $res = $n1 - $n2; break;
                case '*': $res = $n1 * $n2; break;
                case '/': $res = ($n2 != 0) ? $n1 / $n2 : 'Infinity'; break;
            }
            return [
                'status' => 'success',
                'response' => (string)$res,
                'intent' => 'math_calculation'
            ];
        }

        // 2. Coding Tool
        if (str_contains($prompt, 'code') || str_contains($prompt, 'program')) {
            $code = $this->coder->run(['prompt' => $prompt]);
            if (!empty($code)) {
                return [
                    'status' => 'success',
                    'response' => (string)$code,
                    'intent' => 'coding'
                ];
            }
        }

        return null;
    }
}
