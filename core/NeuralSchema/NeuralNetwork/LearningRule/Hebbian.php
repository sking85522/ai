<?php
namespace Core\NeuralSchema\NeuralNetwork\LearningRule;

class HebbianRule {
    /**
     * "Cells that fire together, wire together."
     */
    public function apply(float $pre, float $post): float {
        return $pre * $post;
    }
}
