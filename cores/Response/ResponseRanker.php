<?php
namespace Core\Response;

class ResponseRanker {
    /**
     * Ranks multiple candidate responses and picks the best one.
     */
    public function rank(array $candidates): string {
        if (empty($candidates)) return "";
        
        // Simplified: Pick the first or highest confidence if scores were provided
        usort($candidates, function($a, $b) {
            return ($b['confidence'] ?? 0) <=> ($a['confidence'] ?? 0);
        });

        return $candidates[0]['text'] ?? "";
    }
}
