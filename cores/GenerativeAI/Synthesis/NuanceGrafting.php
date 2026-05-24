<?php
namespace Core\GenerativeAI\Synthesis;

/**
 * HRITIK AI - NUANCE GRAFTING ENGINE
 * Injects sophisticated logical transitions to improve narrative flow.
 */
class NuanceGrafting {
    
    private array $nuances = [
        'contrast' => 'Lekin agar hum doosri taraf dekhein toh, ',
        'addition' => 'Aur iske sath-sath, ',
        'conclusion' => 'Toh basically baat yeh hai ki, ',
        'clarification' => 'Matlab ki, '
    ];

    /**
     * Grafts a nuance into the text based on its structure.
     */
    public function graft(string $text): string {
        if (strlen($text) > 60 && !str_contains($text, ',')) {
            $nuance = $this->nuances['clarification'];
            $parts = explode(' ', $text, (int)(count(explode(' ', $text)) / 2));
            return implode(' ', $parts) . ", " . lcfirst($nuance) . $text;
        }

        return $text;
    }
}
