<?php
namespace Core\NeuralSchema\NeuralNetwork\WeightsBiases;

class WeightManager {
    /**
     * Handles Xavier/He initialization for weights to prevent vanishing gradients.
     */
    public static function initialize(int $fanIn, int $fanOut, string $method = 'xavier'): array {
        $weights = [];
        $stdDev = ($method === 'xavier') ? sqrt(1.0 / $fanIn) : sqrt(2.0 / $fanIn);
        
        for ($i = 0; $i < $fanIn; $i++) {
            for ($j = 0; $j < $fanOut; $j++) {
                $weights[$i][$j] = self::generateGaussian(0, $stdDev);
            }
        }
        return $weights;
    }

    private static function generateGaussian($mean, $stdDev) {
        $u1 = (float)rand() / (float)getrandmax();
        $u2 = (float)rand() / (float)getrandmax();
        $z0 = sqrt(-2.0 * log($u1)) * cos(2.0 * pi() * $u2);
        return $z0 * $stdDev + $mean;
    }
}
