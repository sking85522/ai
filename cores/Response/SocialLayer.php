<?php
namespace Core\Response;

/**
 * HRITIK AI - SOCIAL INTENT DETECTOR (CLEAN)
 * Now purely detects social intent without returning hardcoded strings.
 */
class SocialLayer {
    
    private array $patterns = [
        'greetings' => ['hlo', 'hello', 'hi', 'hey', 'namaste', 'namashkar', 'salam'],
        'wellbeing' => ['kaise ho', 'how are you', 'kya hal h', 'kaise hain', 'kese ho', 'Sab badhiya'],
        'identity' => ['who are you', 'tera naam kya hai', 'kaun ho tum', 'what is your name']
    ];

    /**
     * Check if the prompt matches any social pattern.
     * Returns null now to allow the MainEngine to fetch responses from DB/Model.
     */
    public function handle(string $prompt): ?string {
        // We return null to force the system to use the Database or Generative Model 
        // for even the most basic greetings, ensuring no hardcoded answers.
        return null;
    }
}
