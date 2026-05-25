<?php
namespace Core\NLP\Tone;

/**
 * HRITIK AI - POLITENESS & RESPECT DETECTOR
 * Analyzes the social tone and respect level of the user's input.
 */
class PolitenessDetector {
    
    /**
     * Detects the respect level (Aap vs Tu vs Neutral).
     */
    public function detect(string $text): string {
        if (preg_match('/(aap|please|kripya|sir|ji)/i', $text)) return 'formal_respectful';
        if (preg_match('/(tu|tera|abe)/i', $text)) return 'informal_aggressive';
        return 'neutral_casual';
    }

    /**
     * Calculates a politeness score (0 to 1).
     */
    public function score(string $text): float {
        $score = 0.5;
        if ($this->detect($text) === 'formal_respectful') $score = 0.9;
        if ($this->detect($text) === 'informal_aggressive') $score = 0.2;
        return $score;
    }
}
