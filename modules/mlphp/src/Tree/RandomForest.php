<?php

namespace MLPHP\Tree;

/**
 * Random Forest Classifier — Ensemble of Decision Trees with bootstrap sampling.
 */
class RandomForest
{
    private $trees = [];
    private $nEstimators;
    private $maxDepth;
    private $maxFeatures;

    public function __construct(int $nEstimators = 10, int $maxDepth = 10, ?int $maxFeatures = null)
    {
        $this->nEstimators = $nEstimators;
        $this->maxDepth = $maxDepth;
        $this->maxFeatures = $maxFeatures;
    }

    public function fit(array $X, array $y): self
    {
        $n = count($X);
        $numFeatures = count($X[0]);
        $this->maxFeatures = $this->maxFeatures ?? (int)sqrt($numFeatures);

        for ($t = 0; $t < $this->nEstimators; $t++) {
            // Bootstrap sample
            $indices = [];
            for ($i = 0; $i < $n; $i++) {
                $indices[] = rand(0, $n - 1);
            }

            // Feature bagging
            $allFeatures = range(0, $numFeatures - 1);
            shuffle($allFeatures);
            $selectedFeatures = array_slice($allFeatures, 0, $this->maxFeatures);

            $X_sample = [];
            $y_sample = [];
            foreach ($indices as $idx) {
                $row = [];
                foreach ($selectedFeatures as $f) {
                    $row[] = $X[$idx][$f];
                }
                $X_sample[] = $row;
                $y_sample[] = $y[$idx];
            }

            $tree = new DecisionTree($this->maxDepth);
            $tree->fit($X_sample, $y_sample);
            $this->trees[] = ['tree' => $tree, 'features' => $selectedFeatures];
        }

        return $this;
    }

    public function predict(array $X): array
    {
        $allPredictions = [];

        foreach ($this->trees as $item) {
            $tree = $item['tree'];
            $features = $item['features'];

            $X_subset = [];
            foreach ($X as $sample) {
                $row = [];
                foreach ($features as $f) {
                    $row[] = $sample[$f];
                }
                $X_subset[] = $row;
            }

            $predictions = $tree->predict($X_subset);
            for ($i = 0; $i < count($predictions); $i++) {
                $allPredictions[$i][] = $predictions[$i];
            }
        }

        // Majority vote
        $results = [];
        foreach ($allPredictions as $votes) {
            $counts = array_count_values($votes);
            arsort($counts);
            $results[] = array_key_first($counts);
        }

        return $results;
    }
}
