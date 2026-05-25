<?php
namespace Core\Response;

/**
 * HRITIK AI - TONE CONTROLLER
 * Adjusts AI personality based on user sentiment and manual settings.
 */
class ToneController {
    
    private string $currentTone = 'balanced';

    /**
     * Determine the best tone based on user sentiment.
     */
    public function adjustTone(string $sentiment): void {
        switch ($sentiment) {
            case 'positive':
                $this->currentTone = 'friendly';
                break;
            case 'negative':
                $this->currentTone = 'empathetic';
                break;
            case 'formal':
                $this->currentTone = 'professional';
                break;
            default:
                $this->currentTone = 'balanced';
        }
    }

    /**
     * Apply tone-specific modifiers to a response.
     */
    public function apply(string $text): string {
        switch ($this->currentTone) {
            case 'friendly':
                return ", " . $text . " 😊";
            case 'professional':
                return "" . $text;
            case 'empathetic':
                return ". " . $text;
            case 'funny':
                return "Haha, dekhiye: " . $text . " 😂";
            default:
                return $text;
        }
    }

    public function getTone(): string {
        return $this->currentTone;
    }
}
