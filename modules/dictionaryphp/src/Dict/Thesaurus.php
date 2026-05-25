<?php
namespace DictionaryPHP\Dict;

/**
 * Thesaurus — Synonyms, Antonyms, and Similar word lookup.
 */
class Thesaurus
{
    private static $synonyms = [
        'good' => ['great', 'excellent', 'fine', 'wonderful', 'superb', 'outstanding'],
        'bad' => ['poor', 'terrible', 'awful', 'horrible', 'dreadful', 'atrocious'],
        'happy' => ['joyful', 'cheerful', 'delighted', 'pleased', 'content', 'glad'],
        'sad' => ['unhappy', 'sorrowful', 'dejected', 'gloomy', 'melancholy', 'depressed'],
        'big' => ['large', 'huge', 'enormous', 'massive', 'gigantic', 'vast'],
        'small' => ['tiny', 'little', 'miniature', 'compact', 'petite', 'minor'],
        'fast' => ['quick', 'rapid', 'swift', 'speedy', 'hasty', 'brisk'],
        'slow' => ['sluggish', 'leisurely', 'gradual', 'unhurried', 'lazy', 'dawdling'],
        'smart' => ['intelligent', 'clever', 'brilliant', 'wise', 'sharp', 'bright'],
        'beautiful' => ['gorgeous', 'stunning', 'lovely', 'attractive', 'elegant', 'exquisite'],
        'strong' => ['powerful', 'mighty', 'robust', 'sturdy', 'tough', 'resilient'],
        'easy' => ['simple', 'effortless', 'straightforward', 'uncomplicated', 'painless'],
        'difficult' => ['hard', 'challenging', 'tough', 'complex', 'demanding', 'arduous'],
        'important' => ['significant', 'crucial', 'vital', 'essential', 'critical', 'key'],
        'new' => ['fresh', 'novel', 'modern', 'recent', 'contemporary', 'innovative'],
        'old' => ['ancient', 'aged', 'elderly', 'vintage', 'antique', 'mature'],
        'love' => ['adore', 'cherish', 'treasure', 'appreciate', 'worship', 'idolize'],
        'hate' => ['despise', 'detest', 'loathe', 'abhor', 'dislike', 'resent'],
        'help' => ['assist', 'aid', 'support', 'facilitate', 'enable', 'encourage'],
        'create' => ['build', 'make', 'develop', 'design', 'construct', 'produce'],
    ];

    private static $antonyms = [
        'good' => ['bad', 'poor', 'terrible'],
        'bad' => ['good', 'great', 'excellent'],
        'happy' => ['sad', 'unhappy', 'miserable'],
        'sad' => ['happy', 'joyful', 'cheerful'],
        'big' => ['small', 'tiny', 'little'],
        'small' => ['big', 'large', 'huge'],
        'fast' => ['slow', 'sluggish', 'leisurely'],
        'slow' => ['fast', 'quick', 'rapid'],
        'smart' => ['dumb', 'stupid', 'foolish'],
        'beautiful' => ['ugly', 'hideous', 'unattractive'],
        'strong' => ['weak', 'fragile', 'feeble'],
        'easy' => ['difficult', 'hard', 'challenging'],
        'difficult' => ['easy', 'simple', 'effortless'],
        'new' => ['old', 'ancient', 'outdated'],
        'old' => ['new', 'young', 'modern'],
        'love' => ['hate', 'despise', 'detest'],
        'hate' => ['love', 'adore', 'cherish'],
        'hot' => ['cold', 'cool', 'freezing'],
        'cold' => ['hot', 'warm', 'heated'],
        'true' => ['false', 'incorrect', 'wrong'],
        'false' => ['true', 'correct', 'right'],
    ];

    public static function synonyms(string $word): array
    {
        $word = strtolower(trim($word));
        return self::$synonyms[$word] ?? [];
    }

    public static function antonyms(string $word): array
    {
        $word = strtolower(trim($word));
        return self::$antonyms[$word] ?? [];
    }

    /**
     * Find similar words using Levenshtein distance.
     */
    public static function similar(string $word, int $maxResults = 5): array
    {
        $word = strtolower(trim($word));
        $allWords = array_keys(array_merge(self::$synonyms, self::$antonyms));
        // Also include synonym values
        foreach (self::$synonyms as $syns) {
            $allWords = array_merge($allWords, $syns);
        }
        $allWords = array_unique($allWords);

        $distances = [];
        foreach ($allWords as $candidate) {
            if ($candidate === $word) continue;
            $dist = levenshtein($word, $candidate);
            $distances[$candidate] = $dist;
        }

        asort($distances);
        return array_slice(array_keys($distances), 0, $maxResults);
    }
}
