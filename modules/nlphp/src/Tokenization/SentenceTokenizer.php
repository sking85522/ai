<?php

namespace NLPHP\Tokenization;

class SentenceTokenizer
{
    /**
     * Splits text into sentences based on punctuation (. ! ?).
     * Equivalent to nltk.sent_tokenize.
     */
    public static function tokenize(string $text): array
    {
        // Split on punctuation followed by space or end of string
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])|(?<=[.!?])$/m', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Trim each sentence
        return array_map('trim', $sentences);
    }
}
