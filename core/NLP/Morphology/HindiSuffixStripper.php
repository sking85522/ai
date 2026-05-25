<?php
namespace Core\NLP\Morphology;

/**
 * HRITIK AI - HINDI SUFFIX STRIPPER
 * Advanced morphological analyzer for Hindi and Hinglish roots.
 */
class HindiSuffixStripper {
    
    private array $suffixes = [
        'aa', 'ee', 'iye', 'o', 'na', 'ne', 'ni', 'ta', 'te', 'ti'
    ];

    /**
     * Strips common Hindi suffixes to find the root word.
     */
    public function strip(string $word): string {
        foreach ($this->suffixes as $suffix) {
            if (str_ends_with($word, $suffix)) {
                return substr($word, 0, -strlen($suffix));
            }
        }
        return $word;
    }
}
