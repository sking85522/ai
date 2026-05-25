<?php
namespace Core\NLU;

/**
 * Semantic Matcher
 * Uses TF-IDF + Cosine Similarity to find the most relevant knowledge
 * from the trained data for any given query. This replaces the keyword-based
 * approach with real mathematical similarity scoring.
 */
class SemanticMatcher {

    private TFIDFVectorizer $vectorizer;
    private array $documentVectors = [];
    private array $documentData = [];
    private string $cachePath;
    private bool $ready = false;

    public function __construct() {
        $this->vectorizer = new TFIDFVectorizer();
        $this->cachePath = dirname(__DIR__, 2) . '/storage/nlu/';
    }

    /**
     * Build the semantic index from knowledge shards
     * Only needs to run once (persisted to disk)
     */
    public function buildIndex(int $maxDocs = 5000): void {
        $knowledgePath = dirname(__DIR__, 2) . '/storage/knowledge/';
        $corpus = [];
        $data = [];

        // Load from all categories
        foreach (['general', 'qa', 'logic'] as $cat) {
            $dir = $knowledgePath . $cat . '/';
            if (!is_dir($dir)) continue;

            $shards = glob($dir . 'shard_*.json');
            foreach ($shards as $shardFile) {
                $items = json_decode(file_get_contents($shardFile), true);
                if (!$items) continue;

                foreach ($items as $item) {
                    $q = $item['q'] ?? '';
                    $a = $item['a'] ?? '';
                    if (empty($q) && empty($a)) continue;

                    $combined = $q . ' ' . $a;
                    $corpus[] = $combined;
                    $data[] = [
                        'question' => $q,
                        'answer' => $a,
                        'category' => $cat,
                        'label' => $item['label'] ?? null
                    ];

                    if (count($corpus) >= $maxDocs) break 3;
                }
            }
        }

        if (empty($corpus)) return;

        // Fit TF-IDF on corpus
        $this->vectorizer->fit($corpus);

        // Transform all documents into vectors
        $this->documentVectors = [];
        $this->documentData = [];
        foreach ($corpus as $i => $doc) {
            $vec = $this->vectorizer->transform($doc);
            if (!empty($vec)) {
                $this->documentVectors[$i] = $vec;
                $this->documentData[$i] = $data[$i];
            }
        }

        // Save to disk
        $this->save();
        $this->ready = true;
    }

    /**
     * Search for the most semantically similar entries
     * @param string $query User's question
     * @param int $topN Number of results
     * @return array [['question'=>..., 'answer'=>..., 'score'=>..., 'category'=>...], ...]
     */
    public function search(string $query, int $topN = 5): array {
        if (!$this->ready) {
            $this->loadOrBuild();
        }

        if (!$this->vectorizer->isFitted() || empty($this->documentVectors)) {
            return [];
        }

        // Transform query into TF-IDF vector
        $queryVec = $this->vectorizer->transform($query);
        if (empty($queryVec)) return [];

        // Find similar documents using cosine similarity
        $similar = CosineSimilarity::findSimilar($queryVec, $this->documentVectors, $topN, 0.02);

        $results = [];
        foreach ($similar as $match) {
            $idx = $match['id'];
            $docData = $this->documentData[$idx] ?? null;
            if ($docData) {
                $results[] = [
                    'question' => $docData['question'],
                    'answer' => $docData['answer'],
                    'score' => round($match['score'], 4),
                    'category' => $docData['category'],
                    'label' => $docData['label']
                ];
            }
        }

        return $results;
    }

    /**
     * Get best single answer for a query
     */
    public function getBestAnswer(string $query): ?string {
        $results = $this->search($query, 1);
        if (empty($results)) return null;

        $best = $results[0];
        if ($best['score'] < 0.03) return null; // Too low confidence

        return $best['answer'];
    }

    /**
     * Get multiple answers with synthesis
     */
    public function getSynthesizedAnswer(string $query, int $maxSources = 3): ?array {
        $results = $this->search($query, $maxSources);
        if (empty($results)) return null;

        // Filter to only reasonably confident results
        $results = array_filter($results, fn($r) => $r['score'] > 0.02);
        if (empty($results)) return null;

        return [
            'primary_answer' => $results[0]['answer'],
            'confidence' => $results[0]['score'],
            'category' => $results[0]['category'],
            'supporting' => array_map(fn($r) => $r['answer'], array_slice($results, 1)),
            'sources' => count($results)
        ];
    }

    /**
     * Analyze what the model thinks about a query (for debugging/transparency)
     */
    public function explain(string $query): array {
        $topTerms = $this->vectorizer->getTopTerms($query, 8);
        $results = $this->search($query, 3);

        return [
            'query_terms' => $topTerms,
            'vocab_size' => $this->vectorizer->getVocabSize(),
            'indexed_docs' => count($this->documentVectors),
            'matches' => $results
        ];
    }

    /**
     * Save the semantic index to disk
     */
    private function save(): void {
        if (!is_dir($this->cachePath)) mkdir($this->cachePath, 0777, true);
        
        $this->vectorizer->save();
        file_put_contents($this->cachePath . 'doc_vectors.dat', serialize($this->documentVectors));
        file_put_contents($this->cachePath . 'doc_data.dat', serialize($this->documentData));
    }

    /**
     * Load from disk or build if not exists
     */
    private function loadOrBuild(): void {
        $vecFile = $this->cachePath . 'doc_vectors.dat';
        $dataFile = $this->cachePath . 'doc_data.dat';

        if (file_exists($vecFile) && file_exists($dataFile) && $this->vectorizer->load()) {
            $this->documentVectors = unserialize(file_get_contents($vecFile));
            $this->documentData = unserialize(file_get_contents($dataFile));
            $this->ready = true;
        } else {
            // Auto-build on first use
            $this->buildIndex();
        }
    }

    public function isReady(): bool {
        return $this->ready;
    }
}
