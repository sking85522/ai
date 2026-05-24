<?php
namespace Core\DL\Experimental;

/**
 * HRITIK AI - GRAPH CONVOLUTIONAL LAYER (GCN)
 * Directly processes relational data (graphs) using neighborhood aggregation.
 */
class GraphConvLayer {
    
    /**
     * Aggregates features from neighboring nodes in a graph.
     */
    public function aggregate(array $nodeFeatures, array $adjacency): array {
        $newFeatures = [];
        foreach ($nodeFeatures as $nodeIdx => $features) {
            $neighborSum = $features;
            $neighbors = $adjacency[$nodeIdx] ?? [];
            foreach ($neighbors as $neighborIdx) {
                foreach ($nodeFeatures[$neighborIdx] as $k => $v) {
                    $neighborSum[$k] += $v;
                }
            }
            // Normalize by degree
            $degree = count($neighbors) + 1;
            $newFeatures[$nodeIdx] = array_map(fn($f) => $f / $degree, $neighborSum);
        }
        return $newFeatures;
    }
}
