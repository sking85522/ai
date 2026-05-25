<?php
namespace Core\NLP\Logic;

/**
 * HRITIK AI - NEURAL AMBIGUITY RESOLVER
 * Resolves words with multiple meanings based on surrounding linguistic context.
 */
class AmbiguityResolver {
    
    private array $glossary = [
        'bank' => ['financial', 'river'],
        'bat' => ['sports', 'animal'],
        'achha' => ['good', 'really?', 'okay']
    ];

    /**
     * Resolves the meaning of an ambiguous word.
     */
    public function resolve(string $word, string $context): string {
        if (!isset($this->glossary[$word])) return 'standard';

        // Simplified logic: Search context for related keywords
        if ($word === 'bank' && str_contains($context, 'paise')) return 'financial';
        if ($word === 'bank' && str_contains($context, 'nadi')) return 'river';
        
        return $this->glossary[$word][0];
    }
}
