<?php
namespace Core\GenerativeAI\Knowledge;

/**
 * HRITIK AI - KNOWLEDGE DISTILLER
 * Extracts the most essential facts from raw evidence to prevent generator overload.
 */
class KnowledgeDistiller {
    
    /**
     * Distills a collection of raw evidence into a clean fact-list.
     */
    public function distill(array $evidence): array {
        $facts = [];
        foreach ($evidence as $raw) {
            // Logic: Pick sentences that contain numbers, dates, or proper nouns
            if (preg_match('/[0-9]|Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec/i', $raw)) {
                $facts[] = $raw;
            }
        }
        
        return array_slice(array_unique($facts), 0, 3);
    }
}
