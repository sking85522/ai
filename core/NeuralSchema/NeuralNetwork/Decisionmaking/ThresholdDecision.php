<?php
namespace Core\NeuralSchema\NeuralNetwork\DecisionMaking;

class ThresholdDecision {
    public function decide(float $value, float $threshold = 0.5): bool {
        return $value >= $threshold;
    }
}
