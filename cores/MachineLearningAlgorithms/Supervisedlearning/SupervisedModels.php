<?php
namespace Core\MachineLearningAlgorithms\SupervisedLearning;

/**
 * LinearRegressionModel
 * Ordinary Least Squares Linear Regression
 * Uses matrix math: w = (X^T * X)^-1 * X^T * y
 */
class LinearRegressionModel {
    private array $weights = [];
    private bool $trained = false;

    /**
     * Train using OLS (Ordinary Least Squares)
     */
    public function fit(array $X, array $y): void {
        $n = count($X);
        $features = count($X[0]);

        // Add bias column (ones) to X
        $XBias = [];
        foreach ($X as $row) {
            $XBias[] = array_merge([1], $row);
        }

        // Transpose X
        $XT = $this->transpose($XBias);

        // X^T * X
        $XTX = $this->matmul($XT, $XBias);

        // Inverse of (X^T * X)
        $XTX_inv = $this->invertMatrix($XTX);

        // X^T * y (y as column vector)
        $yCol = array_map(fn($v) => [$v], $y);
        $XTy = $this->matmul($XT, $yCol);

        // Final weights
        $wMatrix = $this->matmul($XTX_inv, $XTy);
        $this->weights = array_map(fn($row) => $row[0], $wMatrix);
        $this->trained = true;
    }

    /**
     * Predict values
     */
    public function predict(array $X): array {
        if (!$this->trained) throw new \Exception("Model not trained.");
        
        $predictions = [];
        foreach ($X as $row) {
            $rowBias = array_merge([1], $row);
            $pred = 0;
            for ($i = 0; $i < count($rowBias); $i++) {
                $pred += $rowBias[$i] * $this->weights[$i];
            }
            $predictions[] = round($pred, 4);
        }
        return $predictions;
    }

    public function getWeights(): array { return $this->weights; }

    // ====== Pure PHP Matrix Utilities ======

    private function transpose(array $m): array {
        $t = [];
        for ($i = 0; $i < count($m[0]); $i++) {
            $row = [];
            for ($j = 0; $j < count($m); $j++) {
                $row[] = $m[$j][$i];
            }
            $t[] = $row;
        }
        return $t;
    }

    private function matmul(array $a, array $b): array {
        $result = [];
        for ($i = 0; $i < count($a); $i++) {
            $row = [];
            for ($j = 0; $j < count($b[0]); $j++) {
                $sum = 0;
                for ($k = 0; $k < count($b); $k++) {
                    $sum += $a[$i][$k] * $b[$k][$j];
                }
                $row[] = $sum;
            }
            $result[] = $row;
        }
        return $result;
    }

    private function invertMatrix(array $m): array {
        $n = count($m);
        $augmented = [];
        for ($i = 0; $i < $n; $i++) {
            $augmented[$i] = $m[$i];
            for ($j = 0; $j < $n; $j++) {
                $augmented[$i][] = ($i === $j) ? 1 : 0;
            }
        }

        // Gauss-Jordan elimination
        for ($col = 0; $col < $n; $col++) {
            // Find pivot
            $maxRow = $col;
            for ($row = $col + 1; $row < $n; $row++) {
                if (abs($augmented[$row][$col]) > abs($augmented[$maxRow][$col])) {
                    $maxRow = $row;
                }
            }
            [$augmented[$col], $augmented[$maxRow]] = [$augmented[$maxRow], $augmented[$col]];

            $pivot = $augmented[$col][$col];
            if (abs($pivot) < 1e-10) throw new \Exception("Matrix is singular.");

            for ($j = 0; $j < 2 * $n; $j++) {
                $augmented[$col][$j] /= $pivot;
            }

            for ($row = 0; $row < $n; $row++) {
                if ($row === $col) continue;
                $factor = $augmented[$row][$col];
                for ($j = 0; $j < 2 * $n; $j++) {
                    $augmented[$row][$j] -= $factor * $augmented[$col][$j];
                }
            }
        }

        $inv = [];
        for ($i = 0; $i < $n; $i++) {
            $inv[] = array_slice($augmented[$i], $n);
        }
        return $inv;
    }
}

/**
 * DecisionTreeClassifier
 * Simple Decision Stump (single split) for binary classification
 */
class DecisionTreeClassifier {
    private ?int $bestFeature = null;
    private ?float $bestThreshold = null;
    private ?string $leftLabel = null;
    private ?string $rightLabel = null;

    /**
     * Train on labeled data
     * $X = [[f1,f2,...], ...], $y = ['label1', 'label2', ...]
     */
    public function fit(array $X, array $y): void {
        $bestGini = PHP_FLOAT_MAX;
        $numFeatures = count($X[0]);
        $n = count($X);

        for ($f = 0; $f < $numFeatures; $f++) {
            $values = array_column($X, $f);
            $sortedVals = array_unique($values);
            sort($sortedVals);

            for ($i = 0; $i < count($sortedVals) - 1; $i++) {
                $threshold = ($sortedVals[$i] + $sortedVals[$i + 1]) / 2;

                $leftLabels = [];
                $rightLabels = [];
                for ($j = 0; $j < $n; $j++) {
                    if ($X[$j][$f] <= $threshold) {
                        $leftLabels[] = $y[$j];
                    } else {
                        $rightLabels[] = $y[$j];
                    }
                }

                $gini = (count($leftLabels) / $n) * $this->giniImpurity($leftLabels) +
                        (count($rightLabels) / $n) * $this->giniImpurity($rightLabels);

                if ($gini < $bestGini) {
                    $bestGini = $gini;
                    $this->bestFeature = $f;
                    $this->bestThreshold = $threshold;
                    $this->leftLabel = $this->majorityVote($leftLabels);
                    $this->rightLabel = $this->majorityVote($rightLabels);
                }
            }
        }
    }

    public function predict(array $X): array {
        $preds = [];
        foreach ($X as $row) {
            $preds[] = $row[$this->bestFeature] <= $this->bestThreshold
                ? $this->leftLabel
                : $this->rightLabel;
        }
        return $preds;
    }

    public function getRule(): string {
        return "if feature[{$this->bestFeature}] <= {$this->bestThreshold} then '{$this->leftLabel}' else '{$this->rightLabel}'";
    }

    private function giniImpurity(array $labels): float {
        $counts = array_count_values($labels);
        $total = count($labels);
        if ($total === 0) return 0;
        $gini = 1.0;
        foreach ($counts as $c) {
            $p = $c / $total;
            $gini -= $p * $p;
        }
        return $gini;
    }

    private function majorityVote(array $labels): string {
        $counts = array_count_values($labels);
        arsort($counts);
        return array_key_first($counts) ?? 'unknown';
    }
}
