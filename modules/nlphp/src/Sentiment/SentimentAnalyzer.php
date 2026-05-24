<?php
namespace NLPHP\Sentiment;

/**
 * Lexicon-based Sentiment Analyzer.
 * Uses a built-in word-score dictionary for positive/negative/neutral classification.
 */
class SentimentAnalyzer
{
    private $lexicon = [];

    public function __construct()
    {
        // Built-in English sentiment lexicon (scores: -5 to +5)
        $this->lexicon = [
            // Strong positive
            'excellent' => 4, 'amazing' => 4, 'outstanding' => 4, 'fantastic' => 4, 'wonderful' => 4,
            'brilliant' => 4, 'superb' => 4, 'perfect' => 5, 'love' => 3, 'best' => 4,
            // Positive
            'good' => 2, 'great' => 3, 'nice' => 2, 'happy' => 2, 'glad' => 2, 'pleased' => 2,
            'like' => 1, 'enjoy' => 2, 'beautiful' => 3, 'awesome' => 3, 'cool' => 1,
            'helpful' => 2, 'useful' => 2, 'easy' => 1, 'fast' => 1, 'reliable' => 2,
            'recommend' => 2, 'impressive' => 3, 'innovative' => 2, 'efficient' => 2,
            'professional' => 2, 'quality' => 2, 'success' => 2, 'thanks' => 1, 'thank' => 1,
            // Negative
            'bad' => -2, 'poor' => -2, 'terrible' => -4, 'horrible' => -4, 'awful' => -4,
            'worst' => -5, 'hate' => -3, 'dislike' => -2, 'ugly' => -2, 'slow' => -1,
            'broken' => -2, 'useless' => -3, 'difficult' => -1, 'confusing' => -2,
            'expensive' => -1, 'disappointed' => -3, 'frustrating' => -3, 'annoying' => -2,
            'waste' => -3, 'fail' => -3, 'failure' => -3, 'error' => -2, 'bug' => -2,
            'crash' => -3, 'problem' => -2, 'issue' => -1, 'wrong' => -2, 'scam' => -5,
            // Intensifiers
            'very' => 0, 'really' => 0, 'extremely' => 0, 'absolutely' => 0, 'totally' => 0,
            // Negators (handled separately)
            'not' => 0, 'never' => 0, 'no' => 0, "don't" => 0, "doesn't" => 0, "isn't" => 0,
        ];
    }

    /**
     * Analyze sentiment of text.
     * @return array ['score' => float, 'label' => 'positive'|'negative'|'neutral', 'compound' => float]
     */
    public function analyze(string $text): array
    {
        $words = preg_split('/\s+/', strtolower(trim($text)));
        $score = 0.0;
        $wordCount = 0;

        // Hash map for O(1) lookups instead of in_array
        $negators = [
            'not' => true, 'never' => true, 'no' => true, "don't" => true,
            "doesn't" => true, "isn't" => true, "wasn't" => true,
            "weren't" => true, "couldn't" => true, "wouldn't" => true
        ];

        $intensifiers = ['very' => 1.5, 'really' => 1.5, 'extremely' => 2.0, 'absolutely' => 2.0, 'totally' => 1.5, 'so' => 1.3];

        $isNegated = false;
        $intensifyMultiplier = 1.0;

        foreach ($words as $word) {
            $word = preg_replace('/[^a-z\']/', '', $word);
            if (empty($word)) continue;

            if (isset($negators[$word])) {
                $isNegated = true;
                continue;
            }

            if (isset($intensifiers[$word])) {
                $intensifyMultiplier = $intensifiers[$word];
                continue;
            }

            if (isset($this->lexicon[$word]) && $this->lexicon[$word] != 0) {
                $wordScore = $this->lexicon[$word] * $intensifyMultiplier;
                if ($isNegated) {
                    $wordScore *= -0.75;
                    $isNegated = false;
                }
                $score += $wordScore;
                $wordCount++;
                $intensifyMultiplier = 1.0;
            } else {
                $isNegated = false;
                $intensifyMultiplier = 1.0;
            }
        }

        // Normalize compound score to -1 to 1 range
        $compound = $score / (sqrt($score * $score + 15));

        if ($compound >= 0.05) $label = 'positive';
        elseif ($compound <= -0.05) $label = 'negative';
        else $label = 'neutral';

        return [
            'score' => round($score, 2),
            'compound' => round($compound, 4),
            'label' => $label,
            'word_scores_found' => $wordCount,
        ];
    }

    /**
     * Add custom words to the lexicon.
     */
    public function addWords(array $words): void
    {
        $this->lexicon = array_merge($this->lexicon, $words);
    }
}
