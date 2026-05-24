<?php
namespace Core\Virtual;

use Core\Memory\BufferedCloudDB;

/**
 * HRITIK AI - LEARNING CORE
 * Converts temporary internet research into permanent cloud knowledge.
 */
class LearningCore {
    
    private BufferedCloudDB $db;

    public function __construct() {
        require_once __DIR__ . '/../Memory/BufferedCloudDB.php';
        $this->db = new BufferedCloudDB();
    }

    /**
     * Ingests a new piece of information into the AI's permanent memory.
     */
    public function learn(string $topic, string $information): void {
        $cleanInfo = strip_tags($information);
        
        // Save to the online database under a new table 'knowledge_memory'
        $this->db->bufferInsert('knowledge_memory', [
            'topic' => trim($topic),
            'content' => trim($cleanInfo)
        ]);
        
        // Optional: Local logging to buffer dir for tracking
        $logDir = dirname(__DIR__, 2) . '/localstorage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
        
        file_put_contents($logDir . '/learning_events.log', 
            "[" . date('Y-m-d H:i:s') . "] Learned about: $topic\n", FILE_APPEND);
    }
}
