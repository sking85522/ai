<?php
namespace Core\NLP\Sentiment;

/**
 * HRITIK AI - NEURAL SENTIMENT ENGINE (ONLINE DB EDITION)
 * Performs sentiment analysis using keywords stored in the remote neural database.
 */
class SentimentEngine {
    
    private static array $keywordCache = [];

    /**
     * Detects sentiment and emotion score using Online DB.
     */
    public function analyze(string $text): array {
        $keywords = $this->getKeywords();
        $score = 0;
        $sentiment = 'neutral';

        foreach ($keywords as $word => $weight) {
            if (preg_match('/\b' . $word . '\b/i', $text)) {
                $score += (float)$weight;
            }
        }

        if ($score > 0) $sentiment = 'positive';
        if ($score < 0) $sentiment = 'negative';

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'intensity' => abs($score)
        ];
    }

    /**
     * Fetches sentiment keywords from Online DB.
     */
    private function getKeywords(): array {
        if (!empty(self::$keywordCache)) return self::$keywordCache;

        require_once __DIR__ . '/../../../online_db.php';
        global $db;
        
        $results = $db->query("SELECT k_key, k_value FROM neural_knowledge WHERE category = 'sentiment'");
        
        if (isset($results['status']) && $results['status'] === 'success' && isset($results['data'])) {
            foreach ($results['data'] as $row) {
                self::$keywordCache[$row['k_key']] = $row['k_value'];
            }
        }

        return self::$keywordCache;
    }
}
