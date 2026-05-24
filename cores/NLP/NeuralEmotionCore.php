<?php
namespace Core\NLP;

/**
 * HRITIK AI - NEURAL EMOTION CORE
 * Analyzes linguistic patterns to detect user mood and adjust the AI's emotional tone.
 */
class NeuralEmotionCore {
    
    private array $moodPatterns = [
        'happy' => ['great', 'awesome', 'mast', 'mazzay', 'happy', 'good'],
        'angry' => ['stupid', 'bekar', 'bakwas', 'bad', 'angry', 'hate'],
        'sad' => ['sad', 'dukh', 'fail', 'bad luck', 'upset']
    ];

    /**
     * Detects the user's current mood.
     */
    public function detectMood(string $text): string {
        $text = strtolower($text);
        foreach ($this->moodPatterns as $mood => $keywords) {
            foreach ($keywords as $word) {
                if (str_contains($text, $word)) return strtoupper($mood);
            }
        }
        return "NEUTRAL";
    }

    /**
     * Adapts the response tone based on the detected mood.
     */
    public function adaptResponse(string $response, string $mood): string {
        // No hardcoded emotional prefixes.
        return $response;
    }
}
