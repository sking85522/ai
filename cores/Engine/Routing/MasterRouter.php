<?php
namespace Core\Engine\Routing;

/**
 * HRITIK AI - MASTER NEURAL ROUTER
 * High-level orchestrator that directs prompts to specialized sub-engines.
 */
class MasterRouter {
    
    /**
     * Routes the prompt to the most efficient sub-system.
     */
    public function route(string $prompt, array $intent): string {
        if ($intent['type'] === 'math') return 'Tools\Math\MathProEngine';
        if ($intent['type'] === 'action') return 'Engine\AgenticCore';
        if ($intent['type'] === 'knowledge') return 'Memory\SemanticSearch';
        
        return 'GenerativeAI\LocalLanguageModel';
    }
}
