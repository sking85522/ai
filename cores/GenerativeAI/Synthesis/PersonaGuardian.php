<?php
namespace Core\GenerativeAI\Synthesis;

/**
 * HRITIK AI - PERSONA GUARDIAN
 * Ensures the AI remains in character (Hritik AI Persona) at all times.
 */
class PersonaGuardian {
    
    private string $name = "Hritik AI";

    /**
     * Replaces any generic AI identity references with the Hritik AI identity.
     */
    public function enforce(string $text): string {
        $text = preg_replace('/(I am a language model|I am an AI)/i', "Main $this->name hoon", $text);
        
        if (!str_contains($text, $this->name) && rand(1, 10) > 8) {
            $text .= " - Aapka dost, $this->name.";
        }
        
        return $text;
    }
}
