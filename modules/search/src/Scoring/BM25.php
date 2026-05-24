<?php

namespace SearchPHP\Scoring;

class BM25
{
    private $k1;
    private $b;
    private $totalDocs;
    private $avgDocLength;

    /**
     * @param int $totalDocs Total number of documents in the corpus
     * @param float $avgDocLength Average document length in the corpus
     * @param float $k1 Term frequency saturation parameter (usually 1.2 to 2.0)
     * @param float $b Length normalization parameter (usually 0.75)
     */
    public function __construct(int $totalDocs, float $avgDocLength, float $k1 = 1.5, float $b = 0.75)
    {
        $this->totalDocs = $totalDocs;
        $this->avgDocLength = $avgDocLength;
        $this->k1 = $k1;
        $this->b = $b;
    }

    /**
     * Inverse Document Frequency (using standard Okapi BM25 formula)
     */
    public function calculateIDF(int $docFreq): float
    {
        // To avoid negative IDF for very frequent terms, we use a smoothed formula
        return log(1 + ($this->totalDocs - $docFreq + 0.5) / ($docFreq + 0.5));
    }

    /**
     * Term Frequency Score
     */
    public function calculateTF(int $termFreq, int $docLength): float
    {
        if ($this->avgDocLength == 0) return 0.0;

        $numerator = $termFreq * ($this->k1 + 1);
        $denominator = $termFreq + $this->k1 * (1 - $this->b + $this->b * ($docLength / $this->avgDocLength));

        return $numerator / $denominator;
    }
}
