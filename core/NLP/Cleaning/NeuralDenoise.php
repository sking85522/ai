<?php
namespace Core\NLP\Cleaning;

/**
 * HRITIK AI - NEURAL DENOISER
 * Removes linguistic noise, redundant symbols, and formatting artifacts.
 */
class NeuralDenoise {
    
    /**
     * Cleans and denoises the raw input text.
     */
    public function clean(string $text): string {
        // Remove repeated punctuation
        $text = preg_replace('/([!?.]){2,}/', '$1', $text);
        
        // Remove special characters that are noise
        $text = preg_replace('/[^\p{L}\p{N}\s,.!?]/u', ' ', $text);
        
        // Final trim and whitespace normalization
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}
