<?php
namespace Core\GenerativeAI\Style;

/**
 * HRITIK AI - NEURAL TONE CONTROLLER
 * Dynamically adjusts the politeness, humor, and formality of the response.
 */
class ToneController {
    
    private float $formality = 0.5;
    private float $humor = 0.2;

    public function setStyle(float $formality, float $humor): void {
        $this->formality = $formality;
        $this->humor = $humor;
    }

    /**
     * Adjusts the response text based on the desired tone.
     */
    public function adjust(string $text): string {
        if ($this->formality > 0.8) {
            $text = "Main aapko suchit karna chahta hoon ki " . lcfirst($text);
        } elseif ($this->humor > 0.7) {
            $text = $text . " (Haha, mazaak tha bhai!)";
        }

        return $text;
    }
}
