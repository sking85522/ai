<?php
namespace Core\Tools\Intelligence;

use Core\SQLGenerator\SQLGenerator;

/**
 * HRITIK AI - CODER TOOL
 * Fetches or generates code snippets dynamically from the Knowledge Base.
 */
class CoderTool {
    private SQLGenerator $sqlGen;

    public function __construct() {
        $this->sqlGen = new SQLGenerator();
    }

    public function run(array $input = []): string {
        $prompt = strtolower((string)($input['prompt'] ?? ''));
        global $db;

        if (!isset($db) || $db === null) {
            return "// Error: Database not connected. Cannot retrieve code samples.";
        }

        // 1. Try to find a specific code sample in the DB
        $sql = $this->sqlGen->generate('search_knowledge', ['query' => $prompt . ' code']);
        $res = $db->query($sql);
        
        if (!empty($res['data'])) {
            return $res['data'][0]['k_value'];
        }

        // 2. Generic fallback if no specific sample exists
        return "// No specific code found in knowledge base for: $prompt. Try asking for a broader topic.";
    }
}
