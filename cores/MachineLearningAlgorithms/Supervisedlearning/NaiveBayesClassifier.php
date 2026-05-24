<?php
namespace Core\MachineLearningAlgorithms\SupervisedLearning;

/**
 * HRITIK AI - NAIVE BAYES CLASSIFIER
 * Fast probabilistic classifier for Intent Detection and Sentiment analysis.
 */
class NaiveBayesClassifier {
    private array $classes = [];
    private array $classCounts = [];
    private array $wordCounts = [];
    private array $vocabulary = [];
    private int $totalDocs = 0;

    /**
     * Train the classifier with a document and its label.
     */
    public function train(array $tokens, string $label): void {
        if (!isset($this->classCounts[$label])) {
            $this->classCounts[$label] = 0;
            $this->wordCounts[$label] = [];
            $this->classes[] = $label;
        }

        $this->classCounts[$label]++;
        $this->totalDocs++;

        foreach ($tokens as $token) {
            $token = strtolower($token);
            $this->vocabulary[$token] = true;
            if (!isset($this->wordCounts[$label][$token])) {
                $this->wordCounts[$label][$token] = 0;
            }
            $this->wordCounts[$label][$token]++;
        }
    }

    /**
     * Predict the class for a set of tokens.
     */
    public function predict(array $tokens): string {
        $bestLabel = 'unknown';
        $maxProb = -INF;
        $vocabSize = count($this->vocabulary);

        foreach ($this->classes as $label) {
            // Prior probability P(Class)
            $logProb = log($this->classCounts[$label] / $this->totalDocs);

            // Conditional probability P(Word|Class) with Laplace smoothing
            $totalWordsInClass = array_sum($this->wordCounts[$label]);
            foreach ($tokens as $token) {
                $token = strtolower($token);
                if (isset($this->vocabulary[$token])) {
                    $count = $this->wordCounts[$label][$token] ?? 0;
                    $logProb += log(($count + 1) / ($totalWordsInClass + $vocabSize));
                }
            }

            if ($logProb > $maxProb) {
                $maxProb = $logProb;
                $bestLabel = $label;
            }
        }

        return $bestLabel;
    }
}
