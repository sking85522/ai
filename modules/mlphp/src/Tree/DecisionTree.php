<?php

namespace MLPHP\Tree;

/**
 * Decision Tree Classifier using ID3/CART-like algorithm.
 * Supports information gain for splitting.
 */
class DecisionTree
{
    private $tree = null;
    private $maxDepth;
    private $minSamples;

    public function __construct(int $maxDepth = 10, int $minSamples = 2)
    {
        $this->maxDepth = $maxDepth;
        $this->minSamples = $minSamples;
    }

    public function fit(array $X, array $y): self
    {
        $data = [];
        for ($i = 0; $i < count($X); $i++) {
            $data[] = ['features' => $X[$i], 'label' => $y[$i]];
        }
        $this->tree = $this->buildTree($data, 0);
        return $this;
    }

    public function predict(array $X): array
    {
        $predictions = [];
        foreach ($X as $sample) {
            $predictions[] = $this->predictSample($sample, $this->tree);
        }
        return $predictions;
    }

    private function buildTree(array $data, int $depth): array
    {
        $labels = array_column($data, 'label');
        $uniqueLabels = array_values(array_unique($labels));

        // Base cases
        if (count($uniqueLabels) === 1) {
            return ['type' => 'leaf', 'value' => $uniqueLabels[0]];
        }
        if ($depth >= $this->maxDepth || count($data) < $this->minSamples) {
            return ['type' => 'leaf', 'value' => $this->majorityVote($labels)];
        }

        $bestSplit = $this->findBestSplit($data);
        if ($bestSplit === null) {
            return ['type' => 'leaf', 'value' => $this->majorityVote($labels)];
        }

        $leftData = [];
        $rightData = [];
        foreach ($data as $row) {
            if ($row['features'][$bestSplit['feature']] <= $bestSplit['threshold']) {
                $leftData[] = $row;
            } else {
                $rightData[] = $row;
            }
        }

        if (empty($leftData) || empty($rightData)) {
            return ['type' => 'leaf', 'value' => $this->majorityVote($labels)];
        }

        return [
            'type' => 'node',
            'feature' => $bestSplit['feature'],
            'threshold' => $bestSplit['threshold'],
            'left' => $this->buildTree($leftData, $depth + 1),
            'right' => $this->buildTree($rightData, $depth + 1),
        ];
    }

    private function findBestSplit(array $data): ?array
    {
        $bestGain = -INF;
        $bestSplit = null;
        $numFeatures = count($data[0]['features']);
        $labels = array_column($data, 'label');
        $parentEntropy = $this->entropy($labels);

        for ($f = 0; $f < $numFeatures; $f++) {
            $values = array_map(fn($row) => $row['features'][$f], $data);
            $uniqueValues = array_unique($values);
            sort($uniqueValues);

            // Use midpoints as candidate thresholds
            for ($i = 0; $i < count($uniqueValues) - 1; $i++) {
                $threshold = ($uniqueValues[$i] + $uniqueValues[$i + 1]) / 2;

                $leftLabels = [];
                $rightLabels = [];
                foreach ($data as $row) {
                    if ($row['features'][$f] <= $threshold) {
                        $leftLabels[] = $row['label'];
                    } else {
                        $rightLabels[] = $row['label'];
                    }
                }

                if (empty($leftLabels) || empty($rightLabels)) continue;

                $gain = $parentEntropy
                    - (count($leftLabels) / count($data)) * $this->entropy($leftLabels)
                    - (count($rightLabels) / count($data)) * $this->entropy($rightLabels);

                if ($gain > $bestGain) {
                    $bestGain = $gain;
                    $bestSplit = ['feature' => $f, 'threshold' => $threshold];
                }
            }
        }

        return $bestSplit;
    }

    private function entropy(array $labels): float
    {
        $counts = array_count_values($labels);
        $total = count($labels);
        $entropy = 0.0;
        foreach ($counts as $count) {
            $p = $count / $total;
            if ($p > 0) {
                $entropy -= $p * log($p, 2);
            }
        }
        return $entropy;
    }

    private function majorityVote(array $labels)
    {
        $counts = array_count_values($labels);
        arsort($counts);
        return array_key_first($counts);
    }

    private function predictSample(array $sample, array $node)
    {
        if ($node['type'] === 'leaf') {
            return $node['value'];
        }
        if ($sample[$node['feature']] <= $node['threshold']) {
            return $this->predictSample($sample, $node['left']);
        }
        return $this->predictSample($sample, $node['right']);
    }

    public function getTree(): ?array
    {
        return $this->tree;
    }
}
