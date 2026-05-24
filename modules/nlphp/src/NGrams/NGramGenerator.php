<?php
namespace NLPHP\NGrams;

/**
 * N-Gram generator — Extracts n-grams from text for language modeling and feature extraction.
 */
class NGramGenerator
{
    /**
     * Generate word-level n-grams.
     * @param string $text Input text
     * @param int $n N-gram size (2 = bigrams, 3 = trigrams)
     * @return array List of n-gram strings
     */
    public static function wordNgrams(string $text, int $n = 2): array
    {
        $words = preg_split('/\s+/', strtolower(trim($text)), -1, PREG_SPLIT_NO_EMPTY);
        $ngrams = [];
        for ($i = 0; $i <= count($words) - $n; $i++) {
            $ngrams[] = implode(' ', array_slice($words, $i, $n));
        }
        return $ngrams;
    }

    /**
     * Generate character-level n-grams.
     * @param string $text Input text
     * @param int $n N-gram size
     * @return array List of character n-gram strings
     */
    public static function charNgrams(string $text, int $n = 3): array
    {
        $text = strtolower(trim($text));
        $ngrams = [];
        $len = mb_strlen($text);
        for ($i = 0; $i <= $len - $n; $i++) {
            $ngrams[] = mb_substr($text, $i, $n);
        }
        return $ngrams;
    }

    /**
     * Get frequency distribution of n-grams.
     */
    public static function frequency(array $ngrams): array
    {
        $freq = array_count_values($ngrams);
        arsort($freq);
        return $freq;
    }
}
