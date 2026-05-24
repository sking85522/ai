<?php

namespace NLPHP\Stemming;

class PorterStemmer
{
    /**
     * Stems a word or array of words.
     * Uses a simplified suffix-stripping approach for demonstration, not the full 1000-line Porter algorithm.
     */
    public static function stem($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'stemWord'], $input);
        }
        return self::stemWord((string)$input);
    }

    private static function stemWord(string $word): string
    {
        $word = strtolower($word);

        if (strlen($word) < 3) {
            return $word;
        }

        // Basic Step 1a: plurals
        if (substr($word, -4) === 'sses') {
            return substr($word, 0, -2); // sses -> ss
        }
        if (substr($word, -3) === 'ies') {
            return substr($word, 0, -3) . 'i'; // ies -> i
        }
        if (substr($word, -2) === 'ss') {
            return $word;
        }
        if (substr($word, -1) === 's') {
            return substr($word, 0, -1);
        }

        // Basic Step 1b: ed, ing
        if (preg_match('/([aeiouy].*)eed$/', $word)) {
            return substr($word, 0, -1); // eed -> ee
        }
        if (preg_match('/([aeiouy].*)ed$/', $word, $matches)) {
            return substr($word, 0, -2);
        }
        if (preg_match('/([aeiouy].*)ing$/', $word, $matches)) {
            return substr($word, 0, -3);
        }

        // Return original if no basic rules matched
        return $word;
    }
}
