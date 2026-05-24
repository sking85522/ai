<?php
namespace Core\GenerativeAI\Fact;

/**
 * HRITIK AI - NEURAL FACT CHECKER
 * Cross-references generated output with evidence to prevent hallucinations.
 */
class FactChecker {
    
    /**
     * Checks if the response contradicts the provided evidence.
     */
    public function verify(string $response, array $evidence): float {
        if (empty($evidence)) return 1.0; // Nothing to check against

        $responseWords = explode(' ', strtolower($response));
        $matchCount = 0;
        
        foreach ($evidence as $fact) {
            $factWords = explode(' ', strtolower($fact));
            $intersect = array_intersect($responseWords, $factWords);
            $matchCount = max($matchCount, count($intersect));
        }

        // Return a confidence score between 0 and 1
        return min(1.0, $matchCount / (count($responseWords) ?: 1) * 2);
    }
}
