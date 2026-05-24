<?php
namespace Core\DL\Activation;

/**
 * HRITIK AI - ADVANCED NEURAL ACTIVATION FUNCTIONS
 * Provides various non-linear transformation functions for deep learning.
 */
class NeuralActivation {
    
    public static function sigmoid(float $x): float {
        return 1 / (1 + exp(-$x));
    }

    public static function relu(float $x): float {
        return max(0, $x);
    }

    public static function leakyRelu(float $x): float {
        return $x > 0 ? $x : 0.01 * $x;
    }

    public static function tanh(float $x): float {
        return tanh($x);
    }

    /**
     * Swish (Self-Gated Activation) - Used in advanced models like EfficientNet.
     */
    public static function swish(float $x): float {
        return $x * self::sigmoid($x);
    }

    public static function softmax(array $values): array {
        $exp = array_map(fn($v) => exp($v - max($values)), $values);
        $sum = array_sum($exp);
        return array_map(fn($v) => $v / ($sum ?: 1), $exp);
    }
}
