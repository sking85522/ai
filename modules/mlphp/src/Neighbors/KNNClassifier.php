<?php

namespace MLPHP\Neighbors;

/**
 * K-Nearest Neighbors Classifier using Euclidean distance.
 */
class KNNClassifier
{
    private $k;
    private $X_train = [];
    private $y_train = [];
    private $weights;

    /**
     * @param int $k Number of neighbors
     * @param string $weights 'uniform' or 'distance'
     */
    public function __construct(int $k = 5, string $weights = 'uniform')
    {
        $this->k = $k;
        $this->weights = $weights;
    }

    public function fit(array $X, array $y): self
    {
        $this->X_train = $X;
        $this->y_train = $y;
        return $this;
    }

    public function predict(array $X): array
    {
        $predictions = [];
        foreach ($X as $sample) {
            $predictions[] = $this->predictSingle($sample);
        }
        return $predictions;
    }

    private function predictSingle(array $sample)
    {
        $distances = [];
        for ($i = 0; $i < count($this->X_train); $i++) {
            $dist = $this->euclideanDistance($sample, $this->X_train[$i]);
            $distances[] = ['index' => $i, 'distance' => $dist];
        }

        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $neighbors = array_slice($distances, 0, $this->k);

        if ($this->weights === 'distance') {
            return $this->weightedVote($neighbors);
        }

        // Uniform voting
        $votes = [];
        foreach ($neighbors as $n) {
            $label = $this->y_train[$n['index']];
            $votes[$label] = ($votes[$label] ?? 0) + 1;
        }
        arsort($votes);
        return array_key_first($votes);
    }

    private function weightedVote(array $neighbors)
    {
        $votes = [];
        foreach ($neighbors as $n) {
            $label = $this->y_train[$n['index']];
            $weight = ($n['distance'] > 0) ? (1.0 / $n['distance']) : 1e10;
            $votes[$label] = ($votes[$label] ?? 0) + $weight;
        }
        arsort($votes);
        return array_key_first($votes);
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += ($a[$i] - $b[$i]) ** 2;
        }
        return sqrt($sum);
    }

    /**
     * Get probability scores for each class.
     */
    public function predictProba(array $X): array
    {
        $results = [];
        foreach ($X as $sample) {
            $distances = [];
            for ($i = 0; $i < count($this->X_train); $i++) {
                $dist = $this->euclideanDistance($sample, $this->X_train[$i]);
                $distances[] = ['index' => $i, 'distance' => $dist];
            }
            usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
            $neighbors = array_slice($distances, 0, $this->k);

            $classCounts = [];
            foreach ($neighbors as $n) {
                $label = $this->y_train[$n['index']];
                $classCounts[$label] = ($classCounts[$label] ?? 0) + 1;
            }
            $proba = [];
            foreach ($classCounts as $label => $count) {
                $proba[$label] = $count / $this->k;
            }
            $results[] = $proba;
        }
        return $results;
    }
}
