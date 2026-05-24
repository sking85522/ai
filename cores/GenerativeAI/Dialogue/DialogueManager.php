<?php
namespace Core\GenerativeAI\Dialogue;

/**
 * HRITIK AI - DIALOGUE MANAGER
 * Maintains the conversational flow and handles multi-turn context tracking.
 */
class DialogueManager {
    
    private array $history = [];

    public function updateHistory(string $prompt, string $response): void {
        $this->history[] = ['q' => $prompt, 'a' => $response];
        if (count($this->history) > 10) array_shift($this->history);
    }

    /**
     * Determines if the current prompt is a follow-up to the previous turn.
     */
    public function isFollowUp(string $prompt): bool {
        $last = end($this->history);
        if (!$last) return false;

        $keywords = ['wo', 'use', 'it', 'him', 'her', 'they', 'aur', 'and', 'then'];
        foreach ($keywords as $word) {
            if (str_contains(strtolower($prompt), $word)) return true;
        }
        return false;
    }
}
