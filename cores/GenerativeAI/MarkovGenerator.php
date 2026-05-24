<?php
namespace Core\GenerativeAI;

/**
 * HRITIK AI - NEURAL MARKOV GENERATOR (PURE DYNAMIC)
 * NO DEFAULT STRINGS. NO STATIC FALLBACKS.
 */
class MarkovGenerator {
    private array $chain = [];
    private array $startWords = [];

    public function learn(string $text): void {
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if (strlen($text) < 5) return;

        $words = explode(' ', strtolower($text));
        $count = count($words);
        if ($count < 2) return;

        $this->startWords[] = $words[0];

        for ($i = 0; $i < $count - 1; $i++) {
            $key = $words[$i];
            $next = $words[$i + 1];
            if (!isset($this->chain[$key])) $this->chain[$key] = [];
            $this->chain[$key][] = $next;
        }
    }

    public function generateFromPrompt(string $prompt, int $maxLength = 25): string {
        if (empty($this->startWords)) {
            return "Hritik AI logic build kar raha hai...";
        }

        // Try to seed with a word from the prompt
        $words = explode(' ', strtolower($prompt));
        $seedCandidates = array_intersect($words, array_keys($this->chain));
        
        $seed = !empty($seedCandidates) ? $seedCandidates[array_rand($seedCandidates)] : $this->startWords[array_rand($this->startWords)];
        
        return $this->generateFromSeed($seed, $maxLength);
    }

    private function generateFromSeed(string $key, int $maxLength): string {
        $sentence = [$key];
        $seen = [$key => 1];
        
        for ($i = 0; $i < $maxLength; $i++) {
            if (!isset($this->chain[$key])) break;
            
            $options = $this->chain[$key];
            // Strict filter: each word can only appear once to prevent loops
            $filteredOptions = array_filter($options, fn($opt) => !isset($seen[$opt]));
            
            if (empty($filteredOptions)) break; // Stop to prevent repetition
            
            $next = $filteredOptions[array_rand($filteredOptions)];
            
            $sentence[] = $next;
            $seen[$next] = 1;
            $key = $next;
            
            // Stop if we hit a punctuation-like end
            if (preg_match('/[.!?]$/', $next)) break;
        }
        
        $result = implode(' ', $sentence);
        return ucfirst(rtrim($result, ' .')) . ".";
    }
}
