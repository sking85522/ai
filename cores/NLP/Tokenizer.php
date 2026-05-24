<?php
namespace Core\NLP;

class Tokenizer {
    /**
     * Splits text into individual word and numeric tokens.
     * Preserves important punctuation like decimals and mathematical signs.
     */
    public function tokenize(string $text): array {
        // Regex to match:
        // 1. Decimals like 12.5
        // 2. Regular words (Unicode support)
        // 3. Mathematical operators
        preg_match_all('/[\p{L}]+|\d+\.\d+|\d+|[\+\-\*\/\=]/u', $text, $matches);
        
        return $matches[0] ?? [];
    }
}
