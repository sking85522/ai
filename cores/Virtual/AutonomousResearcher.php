<?php
namespace Core\Virtual;

use Core\API\ExternalIntelligence;
use Core\Memory\BufferedCloudDB;

/**
 * HRITIK AI - AUTONOMOUS RESEARCHER
 * Pro-actively searches the web to fill knowledge gaps and updates the Cloud Memory.
 */
class AutonomousResearcher {
    
    private ExternalIntelligence $searchEngine;
    private BufferedCloudDB $db;

    public function __construct() {
        require_once dirname(__DIR__) . '/API/ExternalIntelligence.php';
        require_once dirname(__DIR__) . '/Memory/BufferedCloudDB.php';
        
        $this->searchEngine = new ExternalIntelligence();
        $this->db = new BufferedCloudDB();
    }

    /**
     * Conducts research on a topic and saves it to the cloud.
     */
    public function research(string $topic): array {
        // 1. Fetch data from Wikipedia/DuckDuckGo
        $information = $this->searchEngine->search($topic);
        
        // 2. Filter out 'not found' responses
        if (str_contains($information, "couldn't find")) {
            return ['status' => 'error', 'message' => 'No information found online.'];
        }

        // 3. Save to knowledge_memory table (via buffer)
        $this->db->bufferInsert('knowledge_memory', [
            'topic' => $topic,
            'content' => $information,
            'source' => 'Autonomous Research'
        ]);

        // 4. Also save as a prompt/response pair for direct neural retrieval
        $this->db->bufferInsert('neural_memory', [
            'prompt' => "What is $topic?",
            'response' => $information
        ]);

        return [
            'status' => 'success',
            'topic' => $topic,
            'summary' => substr($information, 0, 100) . "..."
        ];
    }
}
