<?php
namespace Core\Tools\Connectivity;

class APIBridgeGenerator {
    public function generateBridge(string $projectPath): string {
        $projectPath = trim($projectPath);
        if ($projectPath === '') {
            return '[BRIDGE] Target project path missing.';
        }

        $root = dirname(__DIR__, 3);
        $targetDir = preg_match('/^[A-Za-z]:[\\\\\/]/', $projectPath) ? $projectPath : $root . DIRECTORY_SEPARATOR . $projectPath;
        if (!is_dir($targetDir)) {
            return '[BRIDGE] Target folder not found: ' . $projectPath;
        }

        $bridge = <<<'PHP'
<?php
function hritik_ai_ask(string $prompt): array {
    $ch = curl_init('http://localhost/hritik_ai/api.php');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode(['prompt' => $prompt])
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode((string)$response, true) ?: ['status' => 'error', 'message' => 'Invalid API response'];
}
PHP;

        file_put_contents($targetDir . DIRECTORY_SEPARATOR . 'hritik_ai_bridge.php', $bridge);
        return '[BRIDGE] hritik_ai_bridge.php created in ' . $projectPath;
    }
}
