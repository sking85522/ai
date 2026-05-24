<?php
namespace SearchPHP\Analysis;

use SearchPHP\Core\Index;

/**
 * Fuzzy matching using Levenshtein distance for typo-tolerant search.
 */
class FuzzyMatcher
{
    public static function search(Index $index, string $query, int $limit = 10, int $maxDistance = 2): array
    {
        $queryWords = preg_split('/\s+/', strtolower(trim($query)));
        $allTerms = $index->getAllTerms();
        $matchedTerms = [];

        foreach ($queryWords as $word) {
            foreach ($allTerms as $term) {
                $distance = levenshtein($word, $term);
                if ($distance <= $maxDistance) {
                    $matchedTerms[] = $term;
                }
            }
        }

        $matchedTerms = array_unique($matchedTerms);
        if (empty($matchedTerms)) {
            return [];
        }

        // Search with expanded query
        $expandedQuery = implode(' ', $matchedTerms);
        return $index->search($expandedQuery, $limit);
    }
}
