<?php
namespace Core\Response;

/**
 * HRITIK AI - CREATIVE ENGINE
 * Specialized in writing poetry, stories, and shayari.
 */
class CreativeEngine {
    
    private $genAi;

    public function __construct($genAi) {
        $this->genAi = $genAi;
    }

    public function write(string $prompt): string {
        $prompt = strtolower($prompt);
        
        if (str_contains($prompt, 'shayari') || str_contains($prompt, 'poem')) {
            return "meri shayari pesh hai:\n\n" . 
                   $this->genAi->generateThought(10, 20, 0.9) . "\n" .
                   $this->genAi->generateThought(10, 20, 0.9) . "\n\n" .
                   "Umeed hai ye aapko pasand aayi hogi.";
        }

        if (str_contains($prompt, 'story') || str_contains($prompt, 'kahani')) {
            return "Ek baar ki baat hai... \n\n" . 
                   $this->genAi->generateThought(15, 30, 0.8) . "\n" .
                   $this->genAi->generateThought(15, 30, 0.8) . "\n\n" .
                   "The End.";
        }

        return $this->genAi->generateThought(5, 15, 0.7);
    }
}
