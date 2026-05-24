<?php
namespace Core\Training\Evaluation;

/**
 * HRITIK AI - NEURAL SCORER
 * Evaluates the performance and intelligence growth of the AI model post-training.
 */
class NeuralScorer {
    
    /**
     * Calculates the intelligence score based on accuracy and loss metrics.
     */
    public function evaluate(float $accuracy, float $loss): float {
        // Simple IQ calculation: (Accuracy * 100) - (Loss * 50)
        $iq = ($accuracy * 100) - ($loss * 50);
        return max(10, min(160, $iq)); // Range: 10 to 160 IQ
    }
}
