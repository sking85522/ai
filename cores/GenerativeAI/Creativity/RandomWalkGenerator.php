<?php
namespace Core\GenerativeAI\Creativity;

/**
 * HRITIK AI - RANDOM WALK GENERATOR
 * Adds a creative spark by exploring less probable but semantically valid neural paths.
 */
class RandomWalkGenerator {
    
    private float $temperature = 0.7; // Higher = More Creative

    public function __construct(float $temp = 0.7) {
        $this->temperature = $temp;
    }

    /**
     * Introduces a "Random Walk" into the candidate selection process.
     */
    public function diversify(array $candidates): string {
        if (empty($candidates)) return "";
        if (count($candidates) === 1) return $candidates[0];

        // If temperature is high, we sometimes pick the 2nd or 3rd best candidate
        if ($this->temperature > 0.8 && rand(1, 10) > 7) {
            return $candidates[array_rand($candidates)];
        }

        return $candidates[0];
    }
}
