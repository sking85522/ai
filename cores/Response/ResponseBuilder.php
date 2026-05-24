<?php
namespace Core\Response;

/**
 * HRITIK AI - NEURAL RESPONSE BUILDER
 * No hardcoded strings. Everything is synthesized from the neural engine.
 */
class ResponseBuilder {

    public function __construct() {
        // No dependencies needed for static responses anymore
    }

    /**
     * Standardizes the response format without adding hardcoded text.
     */
    public function build(string $intent, array $context = [], array $nluData = []): ?string {
        // We no longer return static strings here. 
        // We return null to force the engine to use the NeuralReasoning database.
        return null;
    }

    /**
     * Cleans and formats the raw response from the database or web.
     */
    public function buildWebResponse(string $query, string $snippet, bool $isLocal = false): string {
        // No prefixes like "Mere analysis ke hisaab se". Just the pure knowledge.
        return trim(preg_replace('/\s+/', ' ', $snippet));
    }
}
