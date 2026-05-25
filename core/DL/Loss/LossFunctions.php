<?php
namespace Core\DL\Loss;

/**
 * HRITIK AI - NEURAL LOSS FUNCTIONS
 * Calculates the mathematical "error" between prediction and actual target.
 * Includes gradient computation for backpropagation.
 */
class LossFunctions {

    /**
     * Mean Squared Error loss.
     */
    public static function mse(array $predicted, array $target): float {
        $error = 0;
        foreach ($predicted as $i => $p) {
            $error += pow($p - ($target[$i] ?? 0), 2);
        }
        return $error / (count($predicted) ?: 1);
    }

    /**
     * MSE gradient: d/dp MSE = 2(p - t) / N
     */
    public static function mseGradient(array $predicted, array $target): array {
        $n = count($predicted) ?: 1;
        $grad = [];
        foreach ($predicted as $i => $p) {
            $grad[$i] = 2.0 * ($p - ($target[$i] ?? 0)) / $n;
        }
        return $grad;
    }

    /**
     * Cross-Entropy Loss for classification.
     * Assumes $predicted is already a probability distribution (post-softmax).
     *
     * L = -Σ target[i] · log(predicted[i])
     */
    public static function crossEntropy(array $predicted, array $target): float {
        $loss = 0;
        foreach ($predicted as $i => $p) {
            $p = max(min($p, 1 - 1e-15), 1e-15); // Prevent log(0)
            $loss += ($target[$i] ?? 0) * log($p);
        }
        return -$loss;
    }

    /**
     * Cross-Entropy Loss for a single correct class index.
     * More efficient than the full cross-entropy when target is one-hot.
     *
     * @param float[] $probs     Probability distribution (post-softmax)
     * @param int     $targetIdx Index of the correct class
     * @return float Negative log probability
     */
    public static function crossEntropyIndex(array $probs, int $targetIdx): float {
        $p = max($probs[$targetIdx] ?? 1e-15, 1e-15);
        return -log($p);
    }

    /**
     * Combined Softmax + Cross-Entropy gradient.
     *
     * The gradient of CrossEntropy(Softmax(logits), target_index) is simply:
     *   grad[i] = softmax(logits)[i] - (1 if i == target else 0)
     *
     * This is numerically stable and efficient.
     *
     * @param float[] $logits    Raw logits (pre-softmax)
     * @param int     $targetIdx Index of the correct class
     * @return array ['loss' => float, 'gradient' => float[]]
     */
    public static function softmaxCrossEntropyGrad(array $logits, int $targetIdx): array {
        // Stable softmax
        $max = max($logits);
        $exp = array_map(fn($x) => exp($x - $max), $logits);
        $sum = array_sum($exp);
        if ($sum < 1e-15) $sum = 1e-15;
        $probs = array_map(fn($x) => $x / $sum, $exp);

        // Loss = -log(probs[target])
        $loss = -log(max($probs[$targetIdx], 1e-15));

        // Gradient = probs - one_hot(target)
        $grad = $probs;
        $grad[$targetIdx] -= 1.0;

        return ['loss' => $loss, 'gradient' => $grad];
    }
}
