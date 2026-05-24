<?php

namespace NLPHP\Tokenization;

class WordTokenizer
{
    /**
     * Splits text into individual words, handling basic punctuation.
     * Equivalent to nltk.word_tokenize.
     */
    public static function tokenize(string $text): array
    {
        // Add spaces around punctuation so they become separate tokens
        $text = preg_replace('/([.,!?;:()\[\]{}"])/', ' $1 ', $text);

        // Remove extra whitespaces
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if (empty($text)) {
            return [];
        }

        return explode(' ', $text);
    }
}
