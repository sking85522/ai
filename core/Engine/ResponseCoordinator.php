<?php
namespace Core\Engine;

use Core\GenerativeAI\GenerativeAIAssistant;
use Core\Memory\OnlineCloudMemory;
use Core\Memory\SemanticSearch;
use Core\ML\SupervisedLearner;
use Core\Response\ResponseQualityGuard;

/**
 * HRITIK AI - ADVANCED RESPONSE COORDINATOR
 * Synthesizes knowledge from multiple neural sources for high-coherence output.
 */
class ResponseCoordinator {
    private SemanticSearch $semanticSearch;
    private SupervisedLearner $supervised;
    private ModuleIntegrator $moduleIntegrator;
    private GenerativeAIAssistant $genAi;
    private NeuralReasoning $neuralReasoning;
    private ResponseQualityGuard $guard;

    public function __construct(
        SemanticSearch $semanticSearch,
        SupervisedLearner $supervised,
        ModuleIntegrator $moduleIntegrator,
        GenerativeAIAssistant $genAi,
        NeuralReasoning $neuralReasoning,
        ResponseQualityGuard $guard
    ) {
        $this->semanticSearch = $semanticSearch;
        $this->supervised = $supervised;
        $this->moduleIntegrator = $moduleIntegrator;
        $this->genAi = $genAi;
        $this->neuralReasoning = $neuralReasoning;
        $this->guard = $guard;
    }

    public function respond(string $prompt, array $analysis = []): ?array {
        require_once dirname(__DIR__) . '/Memory/OnlineCloudMemory.php';
        $cloudMemory = new OnlineCloudMemory();
        $tokens = $analysis['processed_prompt']['tokens'] ?? $this->guard->extractKeywords($prompt);
        
        $candidates = [];
        $evidence = [];

        // 1. GATHER ALL POSSIBLE EVIDENCE
        $fact = $this->supervised->findFact($prompt);
        if ($fact) {
            $candidates[] = ['source' => 'supervised_fact', 'intent' => 'knowledge', 'response' => $fact];
            $evidence[] = $fact;
        }

        $semantic = $this->semanticSearch->search($prompt);
        if ($semantic) {
            $candidates[] = ['source' => 'semantic_search', 'intent' => 'knowledge', 'response' => $semantic];
            $evidence[] = $semantic;
        }

        $online = $cloudMemory->searchMemory($prompt);
        if ($online) {
            $candidates[] = ['source' => 'online_cloud_memory', 'intent' => 'knowledge', 'response' => $online];
            $evidence[] = $online;
        }

        // 2. KNOWLEDGE SYNTHESIS (Merging evidence if they are distinct)
        if (count($evidence) > 1) {
            $synthesized = $this->synthesize($evidence);
            $candidates[] = ['source' => 'neural_synthesis', 'intent' => 'knowledge', 'response' => $synthesized, 'boost' => true];
        }

        $module = $this->moduleIntegrator->process($prompt);
        if ($module) {
            $candidates[] = ['source' => 'module_integrator', 'intent' => 'module_execution', 'response' => $module];
        }

        // 3. RANK & SELECT
        $ranked = $this->guard->shortlist($candidates, $prompt, $tokens);
        if (!empty($ranked) && ($ranked[0]['score'] ?? 0) >= 15) {
            $best = $ranked[0];
            $best['evidence_count'] = count($evidence);
            $best['ranked_sources'] = array_column($ranked, 'source');
            return $best;
        }

        // 4. FALLBACK TO GENERATIVE BRAIN
        $generated = $this->genAi->generateThought($prompt, $evidence, $analysis);
        if ($generated) {
            return [
                'source' => 'generative_ai',
                'intent' => 'conversational',
                'response' => $generated,
                'evidence_count' => count($evidence)
            ];
        }

        return null;
    }

    /**
     * Merges multiple pieces of information into a single coherent paragraph.
     */
    private function synthesize(array $evidence): string {
        $unique = array_unique($evidence);
        if (count($unique) === 1) return $unique[0];
        
        // Simple synthesis: Join distinct facts with neural bridges
        return implode(" Aur saath hi, ", $unique);
    }
}
