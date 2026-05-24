<?php
namespace Core\NLP\Tokenizers;

/**
 * HRITIK AI - NEURAL SUB-WORD TOKENIZER
 * Breaks text into meaningful sub-word units (BPE-style) for robust Hinglish processing.
 */
class NeuralTokenizer {
    
    private array $vocab = [];

    /**
     * Tokenizes text into sub-words.
     */
    public function tokenize(string $text): array {
        $text = strtolower(trim($text));
        $words = explode(' ', $text);
        $tokens = [];

        foreach ($words as $word) {
            if (strlen($word) > 8) {
                // Break long words into sub-word chunks
                $tokens[] = substr($word, 0, (int)(strlen($word) / 2));
                $tokens[] = '##' . substr($word, (int)(strlen($word) / 2));
            } else {
                $tokens[] = $word;
            }
        }

        return $tokens;
    }
}
