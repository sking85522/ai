<?php
namespace Core\Training\Feedback;

/**
 * HRITIK AI - HUMAN FEEDBACK BRIDGE (RLHF)
 * Learns from direct human feedback to refine neural response patterns.
 */
class HumanFeedbackBridge {
    
    /**
     * Records feedback for a specific response to improve future generation.
     */
    public function recordFeedback(string $prompt, string $response, bool $isPositive): void {
        require_once __DIR__ . '/../../../online_db.php';
        global $db;
        
        $type = $isPositive ? 'positive_reinforcement' : 'negative_reinforcement';
        $sql = "INSERT INTO neural_knowledge (category, sub_category, k_key, k_value) 
                VALUES ('feedback', '$type', '" . addslashes($prompt) . "', '" . addslashes($response) . "')";
        $db->query($sql);
    }
}
