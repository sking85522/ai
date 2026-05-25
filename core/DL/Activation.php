<?php
namespace Core\DL;

/**
 * HRITIK AI - ACTIVATION FUNCTIONS
 * All neural network activation functions and their derivatives.
 *
 * Scalar versions for per-element use in existing Layer.php.
 * Vector versions in Tensor.php for batch operations.
 */
class Activation {

    // ================================================================
    // Scalar Activations (used by existing DL/Layer.php)
    // ================================================================

    public static function sigmoid(float $x): float {
        $x = max(-500.0, min(500.0, $x));
        return 1.0 / (1.0 + exp(-$x));
    }

    public static function sigmoidDerivative(float $x): float {
        $s = self::sigmoid($x);
        return $s * (1.0 - $s);
    }

    public static function relu(float $x): float {
        return max(0.0, $x);
    }

    public static function reluDerivative(float $x): float {
        return $x > 0.0 ? 1.0 : 0.0;
    }

    public static function tanh(float $x): float {
        return tanh($x);
    }

    public static function tanhDerivative(float $x): float {
        $t = tanh($x);
        return 1.0 - $t * $t;
    }

    // ================================================================
    // Vector Activations (for neural layers operating on arrays)
    // ================================================================

    /**
     * Apply sigmoid to each element of a vector.
     * @param float[] $v Input vector
     * @return float[] Activated vector
     */
    public static function sigmoidVec(array $v): array {
        return array_map(function ($x) {
            $x = max(-500.0, min(500.0, $x));
            return 1.0 / (1.0 + exp(-$x));
        }, $v);
    }

    /**
     * Apply ReLU to each element of a vector.
     */
    public static function reluVec(array $v): array {
        return array_map(fn($x) => max(0.0, $x), $v);
    }

    /**
     * Apply tanh to each element of a vector.
     */
    public static function tanhVec(array $v): array {
        return array_map('tanh', $v);
    }

    /**
     * Softmax: convert logits to probability distribution.
     * Numerically stable (max-subtraction trick).
     *
     * @param float[] $v Logits vector
     * @return float[] Probability distribution (sums to 1.0)
     */
    public static function softmax(array $v): array {
        if (empty($v)) return [];
        $max = max($v);
        $exp = array_map(fn($x) => exp($x - $max), $v);
        $sum = array_sum($exp);
        if ($sum < 1e-15) $sum = 1e-15;
        return array_map(fn($x) => $x / $sum, $exp);
    }

    /**
     * ReLU derivative for a vector (given the ReLU output, not input).
     */
    public static function reluDerivativeVec(array $output): array {
        return array_map(fn($x) => $x > 0.0 ? 1.0 : 0.0, $output);
    }

    /**
     * Sigmoid derivative from sigmoid output: σ'(x) = σ(x)·(1-σ(x))
     */
    public static function sigmoidDerivativeFromOutput(array $output): array {
        return array_map(fn($s) => $s * (1.0 - $s), $output);
    }

    public static function sigmoid(float $x): float
    {
        return 1.0 / (1.0 + exp(-$x));
    }

    public static function relu(float $x): float
    {
        return max(0.0, $x);
    }
}
