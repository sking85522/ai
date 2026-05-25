<?php
namespace Core\Memory\Storage;

use Core\SQLGenerator\SQLGenerator;

/**
 * HRITIK AI - PERMANENT NEURAL MEMORY
 * Saves and retrieves personal facts and preferences about the user.
 */
class PersonalMemoryBridge {
    
    private SQLGenerator $sqlGen;

    public function __construct() {
        $this->sqlGen = new SQLGenerator();
    }

    /**
     * Save a personal fact about the user.
     */
    public function learn(string $prompt, string $fact): void {
        global $db;
        if (!$db) return;

        // Detect personal facts or future promises using broader heuristics
        if (preg_match('/(mera naam|i am|i live in|mujhe .* pasand hai|my name is|is|hai|hota|hoga|rahega|aage main|tum yaad rakhna|meri jankari|tumse swal puchuga|yaad rakh)/i', $prompt)) {
            // Also exclude questions to avoid saving "tumhara naam kya hai" as a fact
            if (!preg_match('/\?|kya|kyun|kaise|how|why|what|when|kaha|kidhar/i', $prompt)) {
                $safeFact = addslashes($fact);
                $safePrompt = addslashes($prompt);
                $sql = "INSERT INTO neural_knowledge (category, sub_category, k_key, k_value) " .
                       "VALUES ('user_profile', 'permanent_memory', '$safePrompt', '$safeFact')";
                $db->query($sql);
            }
        }
    }

    /**
     * Retrieve relevant personal context for the current session.
     */
    public function recall(string $prompt): string {
        global $db;
        if (!$db) return "";

        // Extract keywords from prompt to find related memory
        $words = explode(' ', $prompt);
        $conditions = [];
        foreach ($words as $word) {
            if (strlen($word) > 3) $conditions[] = "k_key LIKE '%" . addslashes($word) . "%'";
        }

        if (empty($conditions)) return "";

        $sql = "SELECT k_value FROM neural_knowledge WHERE category = 'user_profile' AND (" . implode(' OR ', $conditions) . ") LIMIT 2";
        $res = $db->query($sql);
        
        $context = "";
        if (!empty($res['data'])) {
            foreach ($res['data'] as $row) {
                $context .= "User Fact: " . $row['k_value'] . "\n";
            }
        }
        return $context;
    }
}
