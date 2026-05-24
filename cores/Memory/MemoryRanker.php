<?php
namespace Core\Memory;

class MemoryRanker {
    
    /**
     * Ranks memories based on keyword overlap with the current prompt.
     */
    public function rank(array $memories, string $prompt): array {
        $scored = [];
        $queryWords = explode(' ', strtolower($prompt));

        foreach ($memories as $memory) {
            $score = 0;
            $content = strtolower($memory['content'] ?? '');
            foreach ($queryWords as $word) {
                if (strlen($word) > 4 && str_contains($content, $word)) {
                    $score++;
                }
            }
            $memory['score'] = $score;
            $scored[] = $memory;
        }

        // Sort by score descending
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        
        return array_slice($scored, 0, 3); // Return top 3
    }
}
