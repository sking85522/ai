<?php
namespace Core\NLP\Security;

/**
 * HRITIK AI - NEURAL HARM FILTER
 * Detects and filters harmful or offensive content using neural sentiment patterns.
 */
class NeuralHarmFilter {
    
    /**
     * Checks if the text contains harmful sentiment patterns.
     */
    public function isHarmful(string $text, float $toxicityScore): bool {
        // Logic: High negative intensity + specific aggressive keywords = Harmful
        if ($toxicityScore < -0.8) return true;
        
        return preg_match('/(attack|kill|hack|abuse|gaali)/i', $text);
    }
}
