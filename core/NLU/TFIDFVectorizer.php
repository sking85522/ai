<?php
namespace Core\NLU;

/**
 * TF-IDF Vectorizer
 * Converts text into Term Frequency - Inverse Document Frequency vectors.
 * This is the mathematical backbone of semantic understanding.
 */
class TFIDFVectorizer {

    private array $vocabulary = [];     // word => index
    private array $idf = [];            // word => IDF score
    private int $totalDocs = 0;
    private array $docFrequency = [];   // word => number of docs containing it
    private bool $fitted = false;
    private string $persistPath;

    public function __construct() {
        $this->persistPath = dirname(__DIR__, 2) . '/storage/nlu/tfidf_model.dat';
    }

    /**
     * Fit the vectorizer on a corpus of documents
     * @param array $documents Array of strings
     */
    public function fit(array $documents): void {
        $this->totalDocs = count($documents);
        $this->docFrequency = [];
        $this->vocabulary = [];

        // Count document frequency for each term
        foreach ($documents as $doc) {
            $terms = $this->tokenize($doc);
            $uniqueTerms = array_unique($terms);
            foreach ($uniqueTerms as $term) {
                $this->docFrequency[$term] = ($this->docFrequency[$term] ?? 0) + 1;
            }
        }

        // Build vocabulary (only keep terms appearing in at least 2 docs, max 80% of docs)
        $idx = 0;
        $maxDf = $this->totalDocs * 0.8;
        foreach ($this->docFrequency as $term => $df) {
            if ($df >= 2 && $df <= $maxDf && strlen($term) > 1) {
                $this->vocabulary[$term] = $idx++;
            }
        }

        // Calculate IDF: log(N / df) + 1 (smoothed)
        foreach ($this->vocabulary as $term => $idx) {
            $df = $this->docFrequency[$term] ?? 1;
            $this->idf[$term] = log($this->totalDocs / $df) + 1;
        }

        $this->fitted = true;
    }

    /**
     * Transform a single document into a TF-IDF vector
     */
    public function transform(string $document): array {
        $terms = $this->tokenize($document);
        $termCount = count($terms);
        if ($termCount === 0) return [];

        // Count term frequency
        $tf = array_count_values($terms);

        // Build TF-IDF vector
        $vector = [];
        foreach ($this->vocabulary as $term => $idx) {
            $rawTf = $tf[$term] ?? 0;
            $normalizedTf = $rawTf / $termCount; // Normalize by doc length
            $idf = $this->idf[$term] ?? 1;
            $tfidf = $normalizedTf * $idf;
            if ($tfidf > 0) {
                $vector[$term] = $tfidf;
            }
        }

        return $vector;
    }

    /**
     * Transform and return top-N important terms
     */
    public function getTopTerms(string $document, int $n = 10): array {
        $vector = $this->transform($document);
        arsort($vector);
        return array_slice($vector, 0, $n, true);
    }

    /**
     * Get vocabulary size
     */
    public function getVocabSize(): int {
        return count($this->vocabulary);
    }

    public function isFitted(): bool {
        return $this->fitted;
    }

    /**
     * Tokenize text into terms
     */
    private function tokenize(string $text): array {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s\p{L}]/u', ' ', $text);
        $tokens = preg_split('/\s+/', $text);
        return array_filter($tokens, fn($t) => strlen($t) > 1);
    }

    /**
     * Save model to disk
     */
    public function save(): void {
        $dir = dirname($this->persistPath);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $data = [
            'vocabulary' => $this->vocabulary,
            'idf' => $this->idf,
            'totalDocs' => $this->totalDocs,
            'docFrequency' => $this->docFrequency,
            'fitted' => $this->fitted
        ];
        file_put_contents($this->persistPath, serialize($data));
    }

    /**
     * Load model from disk
     */
    public function load(): bool {
        if (!file_exists($this->persistPath)) return false;
        $data = unserialize(file_get_contents($this->persistPath));
        if (!$data) return false;

        $this->vocabulary = $data['vocabulary'];
        $this->idf = $data['idf'];
        $this->totalDocs = $data['totalDocs'];
        $this->docFrequency = $data['docFrequency'];
        $this->fitted = $data['fitted'];
        return true;
    }
}
