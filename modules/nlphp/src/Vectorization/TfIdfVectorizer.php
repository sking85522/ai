<?php
namespace NLPHP\Vectorization;

/**
 * TF-IDF Vectorizer — Converts text documents into TF-IDF feature vectors.
 * Term Frequency–Inverse Document Frequency.
 */
class TfIdfVectorizer
{
    private $vocabulary = [];
    private $idf = [];
    private $documents = [];

    /**
     * Fit the vectorizer on a corpus of documents.
     */
    public function fit(array $documents): self
    {
        $this->documents = $documents;
        $n = count($documents);

        // Build vocabulary and document frequency
        $df = [];
        foreach ($documents as $doc) {
            $words = $this->tokenize($doc);
            $uniqueWords = array_unique($words);
            foreach ($uniqueWords as $word) {
                if (!isset($this->vocabulary[$word])) {
                    $this->vocabulary[$word] = count($this->vocabulary);
                }
                $df[$word] = ($df[$word] ?? 0) + 1;
            }
        }

        // Compute IDF: log((1 + n) / (1 + df)) + 1 (smooth variant)
        foreach ($this->vocabulary as $word => $idx) {
            $this->idf[$word] = log((1 + $n) / (1 + ($df[$word] ?? 0))) + 1;
        }

        return $this;
    }

    /**
     * Transform documents into TF-IDF matrix.
     * @return array 2D array [documents][features]
     */
    public function transform(array $documents): array
    {
        $matrix = [];
        foreach ($documents as $doc) {
            $words = $this->tokenize($doc);
            $wordCount = count($words);
            $tf = array_count_values($words);

            $vector = array_fill(0, count($this->vocabulary), 0.0);
            foreach ($tf as $word => $count) {
                if (isset($this->vocabulary[$word])) {
                    $idx = $this->vocabulary[$word];
                    $termFreq = $count / max(1, $wordCount);
                    $vector[$idx] = $termFreq * ($this->idf[$word] ?? 0);
                }
            }
            $matrix[] = $vector;
        }
        return $matrix;
    }

    /**
     * Fit and transform in one call.
     */
    public function fitTransform(array $documents): array
    {
        $this->fit($documents);
        return $this->transform($documents);
    }

    /**
     * Get the vocabulary (word → index mapping).
     */
    public function getVocabulary(): array
    {
        return $this->vocabulary;
    }

    /**
     * Get feature names (ordered by index).
     */
    public function getFeatureNames(): array
    {
        $names = array_fill(0, count($this->vocabulary), '');
        foreach ($this->vocabulary as $word => $idx) {
            $names[$idx] = $word;
        }
        return $names;
    }

    private function tokenize(string $text): array
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        return preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
}
