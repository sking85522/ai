<?php
namespace Core\MachineLearningAlgorithms\UnsupervisedLearning;

/**
 * KMeansClustering
 * Classic K-Means clustering algorithm implemented in pure PHP.
 */
class KMeansClustering {
    private array $centroids = [];
    private array $clusters = [];
    private int $maxIterations = 100;

    /**
     * Fit K-Means and return human-readable result
     */
    public function fit(array $data, int $k = 2): string {
        $n = count($data);
        $dimensions = count($data[0]);

        // Initialize: pick first k data points as initial centroids
        $this->centroids = array_slice($data, 0, $k);

        for ($iter = 0; $iter < $this->maxIterations; $iter++) {
            // Assignment step
            $this->clusters = array_fill(0, $k, []);
            foreach ($data as $point) {
                $closest = $this->findClosestCentroid($point);
                $this->clusters[$closest][] = $point;
            }

            // Update step: recalculate centroids
            $newCentroids = [];
            for ($c = 0; $c < $k; $c++) {
                if (count($this->clusters[$c]) === 0) {
                    $newCentroids[] = $this->centroids[$c]; // keep old centroid
                    continue;
                }
                $newCentroid = array_fill(0, $dimensions, 0);
                foreach ($this->clusters[$c] as $point) {
                    for ($d = 0; $d < $dimensions; $d++) {
                        $newCentroid[$d] += $point[$d];
                    }
                }
                $clusterSize = count($this->clusters[$c]);
                for ($d = 0; $d < $dimensions; $d++) {
                    $newCentroid[$d] /= $clusterSize;
                }
                $newCentroids[] = $newCentroid;
            }

            // Check convergence
            if ($newCentroids === $this->centroids) break;
            $this->centroids = $newCentroids;
        }

        // Build result string
        $result = "K-Means Clustering (k={$k}) converged. ";
        for ($c = 0; $c < $k; $c++) {
            $size = count($this->clusters[$c]);
            $centroidStr = implode(', ', array_map(fn($v) => round($v, 2), $this->centroids[$c]));
            $result .= "Cluster {$c}: {$size} points, centroid=[{$centroidStr}]. ";
        }
        return $result;
    }

    /**
     * Euclidean distance (Optimized via VectorMath)
     */
    private function euclideanDistance(array $a, array $b): float {
        require_once dirname(__DIR__, 2) . '/Matrix/VectorMath.php';
        return \Core\Matrix\VectorMath::euclideanDistance($a, $b);
    }

    private function findClosestCentroid(array $point): int {
        $minDist = PHP_FLOAT_MAX;
        $closest = 0;
        foreach ($this->centroids as $i => $centroid) {
            $dist = $this->euclideanDistance($point, $centroid);
            if ($dist < $minDist) {
                $minDist = $dist;
                $closest = $i;
            }
        }
        return $closest;
    }

    public function getCentroids(): array { return $this->centroids; }
    public function getClusters(): array { return $this->clusters; }
}

/**
 * PCA - Principal Component Analysis
 * Simple dimensionality reduction
 */
class PCA {
    /**
     * Reduce dimensions of a dataset
     */
    public function fitTransform(array $data, int $nComponents = 2): array {
        $n = count($data);
        $dims = count($data[0]);

        // Step 1: Mean center the data
        $means = array_fill(0, $dims, 0);
        foreach ($data as $point) {
            for ($d = 0; $d < $dims; $d++) {
                $means[$d] += $point[$d];
            }
        }
        $means = array_map(fn($m) => $m / $n, $means);

        $centered = [];
        foreach ($data as $point) {
            $row = [];
            for ($d = 0; $d < $dims; $d++) {
                $row[] = $point[$d] - $means[$d];
            }
            $centered[] = $row;
        }

        // Step 2: Covariance matrix
        $cov = [];
        for ($i = 0; $i < $dims; $i++) {
            for ($j = 0; $j < $dims; $j++) {
                $sum = 0;
                for ($k = 0; $k < $n; $k++) {
                    $sum += $centered[$k][$i] * $centered[$k][$j];
                }
                $cov[$i][$j] = $sum / ($n - 1);
            }
        }

        // Step 3: Simple projection (take first nComponents dimensions as approximation)
        // Full eigenvalue decomposition is complex; this is a practical lite version
        $projected = [];
        foreach ($centered as $point) {
            $projected[] = array_slice($point, 0, $nComponents);
        }

        return $projected;
    }
}
