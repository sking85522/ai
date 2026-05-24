<?php
namespace Core\MachineLearningAlgorithms;

/**
 * HRITIK AI - NEURAL VECTOR CLASSIFIER
 * Advanced text classification using vector similarity and neural weights.
 */
class SimpleTextClassifier {
    
    private array $categories = [];
    private array $featureWeights = [];

    public function __construct() {
        require_once dirname(__DIR__) . '/NLP/Tokenizer.php';
        require_once dirname(__DIR__) . '/Matrix/MatrixOps.php';
    }

    /**
     * Trains the classifier on a labeled dataset.
     */
    public function train(string $text, string $category): void {
        $tokenizer = new \Core\NLP\Tokenizer();
        $tokens = $tokenizer->tokenize($text);
        
        if (!isset($this->categories[$category])) $this->categories[$category] = 0;
        $this->categories[$category]++;

        foreach ($tokens as $token) {
            if (!isset($this->featureWeights[$category][$token])) {
                $this->featureWeights[$category][$token] = 0;
            }
            $this->featureWeights[$category][$token]++;
        }
    }

    /**
     * Predicts the category for a given text.
     */
    public function predict(string $text): string {
        $tokenizer = new \Core\NLP\Tokenizer();
        $tokens = $tokenizer->tokenize($text);
        
        $bestCategory = 'unknown';
        $maxScore = -1;

        foreach ($this->categories as $category => $count) {
            $score = log($count / array_sum($this->categories)); // Prior probability
            
            foreach ($tokens as $token) {
                $tokenFreq = $this->featureWeights[$category][$token] ?? 0;
                $score += log(($tokenFreq + 1) / ($count + count($this->featureWeights[$category] ?? [])));
            }

            if ($score > $maxScore || $bestCategory === 'unknown') {
                $maxScore = $score;
                $bestCategory = $category;
            }
        }

        return $bestCategory;
    }
}
