<?php
namespace Core\NLP;

class InputNormalizer {
    /**
     * Normalizes text to a standard Unicode form (NFKC) and performs case folding.
     */
    public function normalize(string $text): string {
        // 1. Unicode Normalization (NFKC is good for web and mixing symbols)
        if (extension_loaded('intl')) {
            $text = \Normalizer::normalize($text, \Normalizer::FORM_KC);
        }

        // 2. Case folding (More robust than strtolower for some scripts)
        $text = mb_convert_case($text, MB_CASE_LOWER, 'UTF-8');

        // 3. Remove redundant whitespace
        $text = preg_replace('/\s+/u', ' ', $text);
        
        return trim($text);
    }

    /**
     * Specifically handles numeric and decimal normalization for Math tasks.
     */
    public function normalizeNumbers(string $text): string {
        // Ensure decimals like "5. 5" become "5.5"
        return preg_replace('/(\d+)\.\s+(\d+)/u', '$1.$2', $text);
    }
}
