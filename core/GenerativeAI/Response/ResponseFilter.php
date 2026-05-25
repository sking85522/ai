<?php
namespace Core\GenerativeAI\Response;

/**
 * HRITIK AI - NEURAL RESPONSE FILTER & POLISHER
 * Strips robotic artifacts and ensures a natural, bilingual identity-aware tone.
 */
class ResponseFilter {
    
    private array $robotArtifacts = [
        'As an AI language model',
        'In accordance with my programming',
        'I am a machine',
        'based on my training data',
        'I do not have feelings'
    ];

    /**
     * Polishes the raw generated text into a premium Hritik AI response.
     */
    public function polish(string $text): string {
        // 1. Strip Robotic Clichés
        foreach ($this->robotArtifacts as $artifact) {
            $text = str_ireplace($artifact, 'Main Hritik AI hoon aur meri samajh ke hisaab se', $text);
        }

        // 2. Hinglish Smoothing (Neural Bridge)
        $text = $this->applyBilingualFlow($text);

        // 3. Punctuation Fix
        $text = ucfirst(trim($text));
        if (!preg_match('/[.!?]$/u', $text)) $text .= ".";

        return $text;
    }

    private function applyBilingualFlow(string $text): string {
        // Simple heuristic: If sentence is too dry/English, add a natural Hinglish touch
        if (strlen($text) > 20 && !preg_match('/(hai|hoon|kya|theek|achha)/i', $text)) {
            // (Optional logic to inject Hinglish particles)
        }
        return $text;
    }
}
