<?php
namespace Core\GenerativeAI\Reasoning;

/**
 * HRITIK AI - HEURISTIC PRUNING ENGINE
 * Prunes low-quality or nonsensical generative paths during the synthesis phase.
 */
class HeuristicPruning {
    
    /**
     * Filters out candidates that don't fit the logical flow.
     */
    public function prune(array $candidates, array $context): array {
        if (count($candidates) <= 1) return $candidates;

        $pruned = [];
        $contextStr = implode(' ', $context);
        
        foreach ($candidates as $candidate) {
            // Logic: If candidate is a stop-word but context already ended a clause
            if (strlen($candidate) < 2 && count($context) > 10) continue;
            
            // Logic: Prevent immediate word repetition
            if (end($context) === $candidate) continue;

            $pruned[] = $candidate;
        }

        return !empty($pruned) ? $pruned : $candidates;
    }
}
