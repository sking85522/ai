<?php
namespace Core\NLP;

class Stemmer {
    
    // Simple English common suffixes
    private array $enSuffixes = ['ing', 'ly', 'ed', 'es', 's', 'ment'];
    
    // Deep Hinglish common suffixes
    private array $hiSuffixes = [
        'wala', 'wali', 'wale', 'on', 'iye', 'iye', 'o', 'aa', 'ee', 'ne', 
        'raha', 'rahi', 'rahe', 'kar', 'ke', 'do', 'lo', 'ta', 'te', 'ti', 'hain', 'hai'
    ];

    // Native Devanagari Suffixes
    private array $nativeHiSuffixes = [
        'ों', 'ियों', 'ियों', 'ा', 'े', 'ी', 'ो', 'ां', 'ें', 'ीं', 'ु', 'ू'
    ];

    /**
     * Stems a token by removing common suffixes.
     */
    public function stem(string $token): string {
        $len = mb_strlen($token);
        if ($len <= 3) return $token; 

        // English stemming
        foreach ($this->enSuffixes as $suffix) {
            if (str_ends_with($token, $suffix)) {
                return mb_substr($token, 0, -mb_strlen($suffix));
            }
        }

        // Deep Hinglish stemming
        foreach ($this->hiSuffixes as $suffix) {
            if (str_ends_with($token, $suffix)) {
                return mb_substr($token, 0, -mb_strlen($suffix));
            }
        }

        // Native Hindi stemming
        foreach ($this->nativeHiSuffixes as $suffix) {
            if (str_ends_with($token, $suffix)) {
                return mb_substr($token, 0, -mb_strlen($suffix));
            }
        }

        return $token;
    }

    /**
     * Stems an array of tokens.
     */
    public function stemAll(array $tokens): array {
        return array_map([$this, 'stem'], $tokens);
    }
}
