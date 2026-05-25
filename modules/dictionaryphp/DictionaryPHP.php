<?php

namespace DictionaryPHP;

use DictionaryPHP\Dict\LocalDictionary;
use DictionaryPHP\Dict\Thesaurus;

class DictionaryPHP
{
    /**
     * Look up meaning of a word.
     */
    public static function meaning(string $word): ?array
    {
        return LocalDictionary::lookup($word);
    }

    /**
     * Translate English to Hindi.
     */
    public static function translateToHindi(string $word): ?string
    {
        return LocalDictionary::translateEnToHi($word);
    }

    /**
     * Get synonyms for a word.
     */
    public static function synonyms(string $word): array
    {
        return Thesaurus::synonyms($word);
    }

    /**
     * Get antonyms for a word.
     */
    public static function antonyms(string $word): array
    {
        return Thesaurus::antonyms($word);
    }

    /**
     * Check if a word exists in dictionary.
     */
    public static function exists(string $word): bool
    {
        return LocalDictionary::lookup($word) !== null;
    }

    /**
     * Get word type (noun, verb, adjective, etc.).
     */
    public static function wordType(string $word): ?string
    {
        $data = LocalDictionary::lookup($word);
        return $data['type'] ?? null;
    }

    /**
     * Find similar words using Levenshtein distance.
     */
    public static function similar(string $word, int $maxResults = 5): array
    {
        return Thesaurus::similar($word, $maxResults);
    }
}
