<?php
namespace Core\NLP\Knowledge;

/**
 * HRITIK AI - NEURAL FACT EXTRACTOR
 * Extracts Subject-Predicate-Object triples from natural language.
 */
class FactExtractor {
    
    /**
     * Extracts facts and prepares them for database storage.
     */
    public function extract(string $text): array {
        // Simple logic: Extract Subject, Action, and Object
        // Example: "Sachin ne AI banaya" -> [Sachin, banaya, AI]
        
        $tokens = explode(' ', $text);
        if (count($tokens) < 3) return [];

        return [
            'subject' => $tokens[0],
            'predicate' => $tokens[count($tokens) > 2 ? 1 : 0],
            'object' => end($tokens)
        ];
    }
}
