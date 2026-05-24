<?php
/**
 * HRITIK AI - NEURAL TERMINAL v3.5 (PRO EDITION)
 * Enhanced UI with status monitoring and contextual memory feedback.
 */

require_once __DIR__ . '/online_db.php';
require_once __DIR__ . '/core/Bootstrap.php';

use Core\Engine\MainEngine;

// FORCE UNBUFFERED OUTPUT
ob_implicit_flush(true);
while (ob_get_level() > 0) ob_end_flush();

$engine = new MainEngine();

$cyan    = "\033[0;36m";
$gray    = "\033[0;90m";
$white   = "\033[1;37m";
$reset   = "\033[0m";
$yellow  = "\033[1;33m";
$green   = "\033[0;32m";
$red     = "\033[0;31m";
$blue    = "\033[0;34m";

$startTime = time();
$totalWords = 0;
$chatHistory = []; // Local Session Memory

echo "\n" . str_repeat("=", 60) . "\n";
echo "       $cyan HRITIK AI PRO // NEURAL TERMINAL (v3.5) $reset\n";
echo str_repeat("=", 60) . "\n";
echo $gray . " [STATUS] Engine: Online | Database: Connected | Memory: Active" . $reset . "\n\n";

$stdin = defined('STDIN') ? STDIN : fopen('php://stdin', 'r');

while (true) {
    $sessionTime = date('H:i:s', time() - $startTime);
    echo $gray . "[$sessionTime] " . $reset . $white . "> You: " . $reset;
    
    $line = fgets($stdin);
    if ($line === false) break; 
    
    $prompt = trim($line);
    if ($prompt === '') continue; 
    
    if (in_array(strtolower($prompt), ['exit', 'quit', 'bye', 'q'])) {
        echo "\n" . $cyan . " Hritik AI: " . $green . "Alvida bhai! Agli baar milte hain. 👋" . $reset . "\n";
        break;
    }

    // Thinking Execution
    echo $cyan . " Hritik AI: " . $gray . "[Processing Query...] " . $reset;

    $result = $engine->processPrompt($prompt, 'default', null, null);
    
    $response = $result['response'] ?? "Thinking...";
    $source = $result['source'] ?? 'unknown';

    if ($source === 'online_search_api') {
        echo "\r" . $cyan . " Hritik AI: " . $gray . "[Web Data Retrieval...] " . $reset;
    } elseif ($source === 'neural_local_teacher' || $source === 'forced_neural_generation') {
        echo "\r" . $cyan . " Hritik AI: " . $gray . "[Deep Neural Reasoning...] " . $reset;
    }

    // Final Output
    echo "\r" . str_repeat(" ", 50) . "\r";
    echo $cyan . " Hritik AI: " . $green . $response . $reset;

    // Update Local Session Memory
    $chatHistory[] = "User: " . $prompt;
    $chatHistory[] = "AI: " . $response;
    if (count($chatHistory) > 10) array_shift($chatHistory); 

    echo "\n\n";
}
