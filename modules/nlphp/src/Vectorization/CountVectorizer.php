<?php
namespace NLPHP\Vectorization;

/**
 * Bag of Words Vectorizer — Converts text documents into word count vectors.
 */
class CountVectorizer
{
    private $vocabulary = [];

    public function fit(array $documents): self
    {
        foreach ($documents as $doc) {
            $words = $this->tokenize($doc);
            foreach ($words as $word) {
                if (!isset($this->vocabulary[$word])) {
                    $this->vocabulary[$word] = count($this->vocabulary);
                }
            }
        }
        return $this;
    }

    public function transform(array $documents): array
    {
        $matrix = [];
        foreach ($documents as $doc) {
            $words = $this->tokenize($doc);
            $counts = array_count_values($words);
            $vector = array_fill(0, count($this->vocabulary), 0);
            foreach ($counts as $word => $count) {
                if (isset($this->vocabulary[$word])) {
                    $vector[$this->vocabulary[$word]] = $count;
                }
            }
            $matrix[] = $vector;
        }
        return $matrix;
    }

    public function fitTransform(array $documents): array
    {
        $this->fit($documents);
        return $this->transform($documents);
    }

    public function getVocabulary(): array { return $this->vocabulary; }

    private function tokenize(string $text): array
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        return preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
}
