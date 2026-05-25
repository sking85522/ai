<?php

namespace MLPHP\SVM;

/**
 * Support Vector Machine Classifier using simplified SMO (Sequential Minimal Optimization).
 * Linear kernel SVM for binary classification.
 */
class SVC
{
    private $weights = [];
    private $bias = 0.0;
    private $C;
    private $learningRate;
    private $epochs;

    public function __construct(float $C = 1.0, float $learningRate = 0.001, int $epochs = 1000)
    {
        $this->C = $C;
        $this->learningRate = $learningRate;
        $this->epochs = $epochs;
    }

    /**
     * Fit using gradient descent on hinge loss.
     * Labels must be -1 or 1.
     */
    public function fit(array $X, array $y): self
    {
        $n = count($X);
        $nFeatures = count($X[0]);
        $this->weights = array_fill(0, $nFeatures, 0.0);
        $this->bias = 0.0;

        for ($epoch = 0; $epoch < $this->epochs; $epoch++) {
            for ($i = 0; $i < $n; $i++) {
                $linearOutput = $this->dotProduct($this->weights, $X[$i]) + $this->bias;
                $condition = $y[$i] * $linearOutput;

                if ($condition >= 1) {
                    // Correct classification with sufficient margin
                    for ($j = 0; $j < $nFeatures; $j++) {
                        $this->weights[$j] -= $this->learningRate * (2 * (1.0 / $this->epochs) * $this->weights[$j]);
                    }
                } else {
                    // Misclassified or within margin
                    for ($j = 0; $j < $nFeatures; $j++) {
                        $this->weights[$j] -= $this->learningRate * (2 * (1.0 / $this->epochs) * $this->weights[$j] - $this->C * $y[$i] * $X[$i][$j]);
                    }
                    $this->bias -= $this->learningRate * (-$this->C * $y[$i]);
                }
            }
        }

        return $this;
    }

    public function predict(array $X): array
    {
        $predictions = [];
        foreach ($X as $sample) {
            $linearOutput = $this->dotProduct($this->weights, $sample) + $this->bias;
            $predictions[] = $linearOutput >= 0 ? 1 : -1;
        }
        return $predictions;
    }

    public function decisionFunction(array $X): array
    {
        $scores = [];
        foreach ($X as $sample) {
            $scores[] = $this->dotProduct($this->weights, $sample) + $this->bias;
        }
        return $scores;
    }

    private function dotProduct(array $a, array $b): float
    {
        $sum = 0.0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += $a[$i] * $b[$i];
        }
        return $sum;
    }

    public function getWeights(): array { return $this->weights; }
    public function getBias(): float { return $this->bias; }
}
