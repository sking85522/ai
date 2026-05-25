<?php
namespace Core\GenerativeAI\Language;

/**
 * HRITIK AI - HINDI SCRIPT NORMALIZER
 * Converts Devanagari script to Hinglish vectors for engine processing.
 */
class HindiNormalization {
    
    private array $mapping = [
        'है' => 'hai',
        'हूँ' => 'hoon',
        'क्या' => 'kya',
        'कैसे' => 'kaise',
        'नाम' => 'naam'
    ];

    /**
     * Normalizes Hindi script into engine-friendly Hinglish.
     */
    public function normalize(string $text): string {
        foreach ($this->mapping as $hindi => $hinglish) {
            $text = str_replace($hindi, $hinglish, $text);
        }
        return $text;
    }
}
