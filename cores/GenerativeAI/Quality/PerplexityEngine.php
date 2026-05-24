<?php
namespace Core\GenerativeAI\Quality;

/**
 * HRITIK AI - PERPLEXITY ENGINE
 * Measures the logical coherence and predictability of the generated text.
 */
class PerplexityEngine {
    
    /**
     * Calculates a simple perplexity-inspired score for a response.
     */
    public function score(string $text): float {
        $words = explode(' ', strtolower($text));
        $unique = array_unique($words);
        
        // Ratio of unique words to total words (higher = more complex/possibly incoherent)
        $ratio = count($unique) / (count($words) ?: 1);
        
        // If ratio is too high (>0.9) or too low (<0.2), quality is suspect
        return $ratio;
    }
}
