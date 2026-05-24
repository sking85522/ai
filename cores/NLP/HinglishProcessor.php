<?php
namespace Core\NLP;

/**
 * HRITIK AI - ADVANCED HINGLISH PROCESSOR
 * Handles the nuances of bilingual (Hindi-English) conversational flows.
 */
class HinglishProcessor {
    
    private array $normalizationMap = [
        'shi' => 'sahi',
        'theek' => 'thik',
        'kro' => 'karo',
        'krna' => 'karna',
        'btao' => 'batao',
        'dikhao' => 'dikhaye',
        'kese' => 'kaise',
        'vha' => 'wahan',
        'jha' => 'jahan',
        'moka' => 'mauka',
        'jrurat' => 'zaroorat'
    ];

    /**
     * Normalizes Hinglish words to a standard form for better matching.
     */
    public function normalize(string $text): string {
        $words = explode(' ', strtolower($text));
        $normalized = [];
        foreach ($words as $word) {
            $normalized[] = $this->normalizationMap[$word] ?? $word;
        }
        
        $text = implode(' ', $normalized);
        
        // Advanced Suffix Stripping for Hindi Verbs in Hinglish
        // Strips common endings like -o, -iye, -na, -ke, -raha
        return preg_replace('/(iye|oge|ega|ke|raha|rha|na)$/u', '', $text);
    }

    /**
     * Detects if the sentence is primarily Hinglish.
     */
    public function detectHinglish(string $text): bool {
        $hinglishMarkers = ['hai', 'hain', 'tha', 'thi', 'the', 'kya', 'kaise', 'kyu', 'aur', 'toh', 'sahi', 'yaar', 'bhai'];
        $words = explode(' ', strtolower($text));
        $matches = array_intersect($words, $hinglishMarkers);
        return count($matches) > 0;
    }

    /**
     * Maps informal Hinglish commands to system actions.
     */
    public function mapIntent(string $text): string {
        if (preg_match('/(theek|fix|shi|thik|banao|update)/u', $text)) return 'ACTION_FIX';
        if (preg_match('/(dikhaye|check|check kr|analyze)/u', $text)) return 'ACTION_INSPECT';
        if (preg_match('/(batao|pucho|query|sawal)/u', $text)) return 'QUERY_INFORMATIONAL';
        return 'UNKNOWN';
    }
}
