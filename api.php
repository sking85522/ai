<?php
/**
 * HRITIK AI - WEB API BRIDGE
 * Connects the frontend to the Neural Engine.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/online_db.php';
require_once __DIR__ . '/core/Bootstrap.php';

use Core\Engine\MainEngine;

// Handle Stats Request
if (isset($_GET['action']) && $_GET['action'] === 'stats') {
    global $db;
    $res = $db->query("SELECT COUNT(*) as total FROM neural_memory");
    echo json_encode(['memory_count' => $res['data'][0]['total'] ?? 0]);
    exit;
}

// Get the raw POST data
$rawData = file_get_contents(php_sapi_name() === 'cli' ? 'php://stdin' : 'php://input');
$rawData = preg_replace('/^\xEF\xBB\xBF/', '', (string)$rawData);
$data = json_decode($rawData, true);

if (!isset($data['prompt']) || empty(trim($data['prompt']))) {
    echo json_encode(['status' => 'error', 'message' => 'Empty prompt']);
    exit;
}

$engine = new MainEngine();
$prompt = $data['prompt'];

// Process the prompt
// Note: We don't use streaming for the basic API to keep JSON response clean.
$result = $engine->processPrompt($prompt);

// Extract Mood (We'll re-run analyzer here or modify Engine to return it)
// Currently Engine returns the response with the tone prefix.
// Let's also detect mood specifically for the UI.
$moodAnalyzer = new \Core\NLP\NeuralMoodAnalyzer();
$detectedMood = $moodAnalyzer->analyze($prompt);

// Handle [CMD_EXEC] in web context (Display only or restricted execution)
$response = $result['response'];
if (str_contains($response, "[CMD_EXEC]")) {
    $parts = explode("[CMD_EXEC]", $response);
    $cmd = trim($parts[1] ?? '');
    
    // In Web API, we capture the command output instead of just printing it
    if ($cmd) {
        if (str_starts_with(strtolower($cmd), 'php') || str_ends_with(strtolower($cmd), '.php')) {
            $fullPath = "h:\\xampp\\php\\php.exe " . str_replace('php ', '', $cmd);
        } else {
            // For Claw or other binaries
            $fullPath = $cmd;
        }
        
        $output = shell_exec($fullPath . " 2>&1");
        $response = str_replace("[CMD_EXEC] $cmd", "\n---\nCommand Output:\n" . $output, $response);
    }
}

echo json_encode([
    'status' => 'success',
    'response' => $response,
    'mood' => $detectedMood,
    'intent' => $result['intent'] ?? 'general',
    'confidence' => $result['confidence'] ?? null,
    'source' => $result['source'] ?? null,
    'teacher_used' => $result['teacher_used'] ?? false,
    'evidence' => $result['evidence'] ?? []
]);
