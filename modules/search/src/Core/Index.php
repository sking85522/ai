<?php

namespace SearchPHP\Core;

use SearchPHP\Analysis\Analyzer;
use SearchPHP\Scoring\BM25;

class Index
{
    private $documents = [];
    private $invertedIndex = []; // [term => [docId => frequency]]
    private $docLengths = []; // [docId => total_terms_count]
    private $analyzer;
    private $averageDocLength = 0;
    private $totalTermsInAllDocs = 0;

    public function __construct()
    {
        $this->analyzer = new Analyzer();
    }

    public function addDocument(Document $doc)
    {
        $docId = $doc->getId();
        $this->documents[$docId] = $doc;

        $termsCount = 0;

        // Index all fields
        foreach ($doc->getFields() as $fieldName => $content) {
            if (is_string($content)) {
                $tokens = $this->analyzer->analyze($content);
                $termsCount += count($tokens);

                foreach ($tokens as $token) {
                    if (!isset($this->invertedIndex[$token])) {
                        $this->invertedIndex[$token] = [];
                    }
                    if (!isset($this->invertedIndex[$token][$docId])) {
                        $this->invertedIndex[$token][$docId] = 0;
                    }
                    $this->invertedIndex[$token][$docId]++;
                }
            }
        }

        $this->docLengths[$docId] = $termsCount;
        $this->totalTermsInAllDocs += $termsCount;
        $this->averageDocLength = $this->totalTermsInAllDocs / count($this->documents);
    }

    public function search(string $query, int $limit = 10): array
    {
        $queryTokens = $this->analyzer->analyze($query);
        $scores = [];

        $bm25 = new BM25(count($this->documents), $this->averageDocLength);

        foreach ($queryTokens as $token) {
            if (isset($this->invertedIndex[$token])) {
                $docFreq = count($this->invertedIndex[$token]); // Number of docs containing this term
                $idf = $bm25->calculateIDF($docFreq);

                foreach ($this->invertedIndex[$token] as $docId => $termFreq) {
                    $docLength = $this->docLengths[$docId];
                    $tfScore = $bm25->calculateTF($termFreq, $docLength);

                    if (!isset($scores[$docId])) {
                        $scores[$docId] = 0;
                    }

                    // BM25 Score = IDF * TF
                    $scores[$docId] += $idf * $tfScore;
                }
            }
        }

        // Sort by score descending
        arsort($scores);

        $results = [];
        $count = 0;
        foreach ($scores as $docId => $score) {
            if ($count >= $limit) break;
            $results[] = [
                'document' => $this->documents[$docId],
                'score' => $score
            ];
            $count++;
        }

        return $results;
    }
    public function save(string $filePath)
    {
        $data = [
            'documents' => $this->documents,
            'invertedIndex' => $this->invertedIndex,
            'docLengths' => $this->docLengths,
            'averageDocLength' => $this->averageDocLength,
            'totalTermsInAllDocs' => $this->totalTermsInAllDocs
        ];
        file_put_contents($filePath, serialize($data));
    }

    public function load(string $filePath)
    {
        if (!file_exists($filePath)) return;
        $data = unserialize(file_get_contents($filePath));
        if ($data) {
            $this->documents = $data['documents'];
            $this->invertedIndex = $data['invertedIndex'];
            $this->docLengths = $data['docLengths'];
            $this->averageDocLength = $data['averageDocLength'];
            $this->totalTermsInAllDocs = $data['totalTermsInAllDocs'];
        }
    }
}
