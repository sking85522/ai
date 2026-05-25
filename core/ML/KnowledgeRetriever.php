<?php
namespace Core\ML;

/**
 * KnowledgeRetriever
 * Searches the trained knowledge shards (SNLI, MSMARCO, General) 
 * using BM25 index + direct shard scanning to find answers.
 */
class KnowledgeRetriever {

    private $knowledgePath;
    private $searchEngine = null;

    public function __construct() {
        $this->knowledgePath = dirname(__DIR__, 2) . '/storage/knowledge/';
    }

    /**
     * Search all knowledge categories for the best answer
     * Returns null if nothing relevant found
     */
    public function search(string $query, int $maxResults = 3): ?array {
        $query = strtolower(trim($query));
        if (strlen($query) < 2) return null;

        $results = [];

        // Strategy 1: BM25 Index Search (fast, ranked)
        $indexResults = $this->searchBM25($query);
        if (!empty($indexResults)) {
            $results = array_merge($results, $indexResults);
        }

        // Strategy 2: Direct keyword scan (deeper, catches what BM25 misses)
        $directResults = $this->searchDirect($query);
        if (!empty($directResults)) {
            $results = array_merge($results, $directResults);
        }

        if (empty($results)) return null;

        // De-duplicate and rank
        $results = $this->rankResults($results, $query);

        return array_slice($results, 0, $maxResults);
    }

    /**
     * Get a single best answer as a string
     */
    public function getAnswer(string $query): ?string {
        $results = $this->search($query, 1);
        if (!$results || empty($results)) return null;

        $best = $results[0];
        return $best['answer'] ?? null;
    }

    /**
     * Search using BM25 indexes
     */
    private function searchBM25(string $query): array {
        $results = [];
        $categories = ['general', 'qa', 'logic'];

        foreach ($categories as $cat) {
            $indexPath = $this->knowledgePath . $cat . '.idx';
            if (!file_exists($indexPath)) continue;

            try {
                require_once dirname(__DIR__, 2) . '/modules/search/autoload.php';
                $search = new \SearchPHP\SearchPHP();
                $search->loadIndex($indexPath);

                $hits = $search->search($query, 5);
                foreach ($hits as $hit) {
                    $doc = $hit['document'];
                    $score = $hit['score'] ?? 0;
                    $fields = $doc->getFields();
                    
                    $results[] = [
                        'question' => $fields['question'] ?? '',
                        'answer' => $fields['answer'] ?? '',
                        'score' => $score,
                        'source' => "bm25_{$cat}"
                    ];
                }
            } catch (\Exception $e) {
                error_log("KnowledgeRetriever BM25 error ({$cat}): " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Direct keyword search through shards (slower but catches more)
     * Only scans first 50 shards per category to stay fast
     */
    private function searchDirect(string $query): array {
        $results = [];
        $queryWords = array_filter(explode(' ', $query), fn($w) => strlen($w) > 2);
        if (empty($queryWords)) return [];

        $categories = ['general', 'qa', 'logic'];

        foreach ($categories as $cat) {
            $dir = $this->knowledgePath . $cat . '/';
            if (!is_dir($dir)) continue;

            $shards = glob($dir . 'shard_*.json');
            // Limit scan for performance
            $shards = array_slice($shards, 0, 50);

            foreach ($shards as $shardFile) {
                $data = json_decode(file_get_contents($shardFile), true);
                if (!$data) continue;

                foreach ($data as $item) {
                    $q = strtolower($item['q'] ?? '');
                    $a = strtolower($item['a'] ?? '');
                    $combined = $q . ' ' . $a;

                    // Count matching words
                    $matchCount = 0;
                    foreach ($queryWords as $word) {
                        if (strpos($combined, $word) !== false) {
                            $matchCount++;
                        }
                    }

                    // Require at least 2 matching words (or 1 if query is short)
                    $minMatch = count($queryWords) <= 2 ? 1 : 2;
                    if ($matchCount >= $minMatch) {
                        $relevance = $matchCount / count($queryWords);
                        $results[] = [
                            'question' => $item['q'] ?? '',
                            'answer' => $item['a'] ?? '',
                            'score' => $relevance,
                            'source' => "direct_{$cat}"
                        ];
                    }

                    // Don't accumulate too many results
                    if (count($results) > 20) break 2;
                }
            }
        }

        return $results;
    }

    /**
     * Rank results by relevance score and de-duplicate
     */
    private function rankResults(array $results, string $query): array {
        // De-duplicate by answer text
        $seen = [];
        $unique = [];
        foreach ($results as $r) {
            $key = md5(strtolower($r['answer']));
            if (!isset($seen[$key])) {
                $seen[$key] = true;

                // Boost score for exact query word matches in question
                $qWords = explode(' ', strtolower($query));
                $questionLower = strtolower($r['question']);
                $bonus = 0;
                foreach ($qWords as $w) {
                    if (strlen($w) > 3 && strpos($questionLower, $w) !== false) {
                        $bonus += 0.3;
                    }
                }
                $r['score'] += $bonus;

                // Penalize very short answers
                if (strlen($r['answer']) < 10) {
                    $r['score'] *= 0.5;
                }

                $unique[] = $r;
            }
        }

        // Sort by score descending
        usort($unique, fn($a, $b) => $b['score'] <=> $a['score']);

        return $unique;
    }
}
