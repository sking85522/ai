<?php
namespace Core\NLP;

class TextCleaner {
    
    private array $slangMap = [
        'nhi' => 'nahi',
        'nhii' => 'nahi',
        'kyu' => 'kyun',
        'kyoo' => 'kyun',
        'h' => 'hai',
        'thk' => 'thik',
        'kk' => 'ok',
        'gn' => 'goodnight',
        'gm' => 'goodmorning',
        'kya' => 'kya',
        'kaun' => 'kaun',
        'kon' => 'kaun',
        'kab' => 'kab',
        'kese' => 'kaise',
        'aisa' => 'aisa',
        'krna' => 'karna',
        'kr' => 'kar',
        'kro' => 'karo',
        'rha' => 'raha',
        'rhi' => 'rahi'
    ];

    /**
     * Cleans raw input text: removes special characters, normalizing spaces, etc.
     */
    public function clean(string $text): string {
        // 1. Lowercase for consistency
        $text = mb_strtolower($text, 'UTF-8');

        // 2. Remove emojis
        $text = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $text);

        // 3. Remove most special characters but keep alphanumeric and spaces
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // 4. Normalize spaces
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // 5. Slang Normalization (Split, Map, Join)
        $words = explode(' ', $text);
        foreach ($words as &$word) {
            if (isset($this->slangMap[$word])) {
                $word = $this->slangMap[$word];
            }
        }
        
        return implode(' ', $words);
    }
}
