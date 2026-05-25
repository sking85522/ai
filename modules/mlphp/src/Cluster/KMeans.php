<?php

namespace MLPHP\Cluster;

use NumPHP\Core\NDArray;
use NumPHP\NumPHP as np;

class KMeans
{
    private $n_clusters;
    private $max_iter;
    private $cluster_centers = [];

    public function __construct(int $n_clusters = 8, int $max_iter = 300)
    {
        $this->n_clusters = $n_clusters;
        $this->max_iter = $max_iter;
    }

    private function euclideanDistance(array $point1, array $point2): float
    {
        $sum = 0.0;
        for ($i = 0; $i < count($point1); $i++) {
            $sum += pow($point1[$i] - $point2[$i], 2);
        }
        return sqrt($sum);
    }

    /**
     * Compute k-means clustering.
     */
    public function fit($X)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $n_samples = count($X_data);
        $n_features = count($X_data[0]);

        // Initialize centroids randomly from the dataset
        $indices = array_rand($X_data, $this->n_clusters);
        if (!is_array($indices)) $indices = [$indices];
        foreach ($indices as $idx) {
            $this->cluster_centers[] = $X_data[$idx];
        }

        $labels = array_fill(0, $n_samples, -1);

        for ($iter = 0; $iter < $this->max_iter; $iter++) {
            $clusters = array_fill(0, $this->n_clusters, []);

            // Assign points to the nearest centroid
            $is_converged = true;
            for ($i = 0; $i < $n_samples; $i++) {
                $min_dist = INF;
                $closest_cluster = -1;

                for ($j = 0; $j < $this->n_clusters; $j++) {
                    $dist = $this->euclideanDistance($X_data[$i], $this->cluster_centers[$j]);
                    if ($dist < $min_dist) {
                        $min_dist = $dist;
                        $closest_cluster = $j;
                    }
                }

                if ($labels[$i] !== $closest_cluster) {
                    $is_converged = false;
                }

                $labels[$i] = $closest_cluster;
                $clusters[$closest_cluster][] = $X_data[$i];
            }

            if ($is_converged) {
                break;
            }

            // Update centroids
            for ($k = 0; $k < $this->n_clusters; $k++) {
                if (count($clusters[$k]) > 0) {
                    $new_centroid = array_fill(0, $n_features, 0.0);
                    foreach ($clusters[$k] as $point) {
                        for ($f = 0; $f < $n_features; $f++) {
                            $new_centroid[$f] += $point[$f];
                        }
                    }
                    for ($f = 0; $f < $n_features; $f++) {
                        $new_centroid[$f] /= count($clusters[$k]);
                    }
                    $this->cluster_centers[$k] = $new_centroid;
                }
            }
        }

        return $this;
    }

    /**
     * Predict the closest cluster each sample in X belongs to.
     */
    public function predict($X)
    {
        $X_data = ($X instanceof NDArray) ? $X->getData() : $X;
        $n_samples = count($X_data);
        $predictions = [];

        for ($i = 0; $i < $n_samples; $i++) {
            $min_dist = INF;
            $closest_cluster = -1;

            for ($j = 0; $j < $this->n_clusters; $j++) {
                $dist = $this->euclideanDistance($X_data[$i], $this->cluster_centers[$j]);
                if ($dist < $min_dist) {
                    $min_dist = $dist;
                    $closest_cluster = $j;
                }
            }

            $predictions[] = $closest_cluster;
        }

        return np::array($predictions);
    }
}
