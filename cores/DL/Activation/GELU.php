<?php
namespace Core\DL\Activation;

/**
 * HRITIK AI - GELU ACTIVATION (GAUSSIAN ERROR LINEAR UNIT)
 * High-performance activation used in state-of-the-art Transformers (GPT, BERT).
 */
class GELU {
    
    /**
     * Approximation of the GELU function.
     */
    public static function compute(float $x): float {
        return 0.5 * $x * (1 + tanh(sqrt(2 / pi()) * ($x + 0.044715 * pow($x, 3))));
    }
}
