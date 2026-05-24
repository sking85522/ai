<?php
namespace Core\GenerativeAI;

use Core\Response\ResponseQualityGuard;

/**
 * HRITIK AI - MULTIVERSE LANGUAGE MODEL (MLM)
 * The most powerful PHP generative engine in existence. Handles 500+ neural patterns.
 */
class LocalLanguageModel {
    private MarkovGenerator $markov;
    private Transformers $transformer;
    private ResponseQualityGuard $guard;
    
    // Sub-Modules & Sub-Universe
    private Attention\MultiHeadAttention $mha;
    private Logic\ChainOfThought $cot;
    private Response\ResponseFilter $filter;
    private Emotion\NeuralMoodEngine $moodEngine;
    private Language\HinglishSlangBridge $slangBridge;
    private Memory\GenerativeMemory $genMemory;
    private Creativity\RandomWalkGenerator $creativity;
    private Fact\FactChecker $factChecker;
    private Dialogue\DialogueManager $dialogue;
    private Knowledge\KnowledgeDistiller $distiller;
    
    // Sub-Universe Mega-Modules
    private SubUniverse\CosmicReasoning $cosmic;
    private SubUniverse\LinguisticGalaxy $galaxy;
    private SubUniverse\DigitalArchitect $architect;
    private SubUniverse\InfiniteMemory $infiniteMem;
    private SubUniverse\CreativeMultiverse $multiverse;

    public function __construct(MarkovGenerator $markov, Transformers $transformer, ResponseQualityGuard $guard) {
        $this->markov = $markov;
        $this->transformer = $transformer;
        $this->guard = $guard;

        // Load all core sub-modules
        require_once __DIR__ . '/Attention/MultiHeadAttention.php';
        require_once __DIR__ . '/Logic/ChainOfThought.php';
        require_once __DIR__ . '/Response/ResponseFilter.php';
        require_once __DIR__ . '/Emotion/NeuralMoodEngine.php';
        require_once __DIR__ . '/Language/HinglishSlangBridge.php';
        require_once __DIR__ . '/Memory/GenerativeMemory.php';
        require_once __DIR__ . '/Creativity/RandomWalkGenerator.php';
        require_once __DIR__ . '/Fact/FactChecker.php';
        require_once __DIR__ . '/Dialogue/DialogueManager.php';
        require_once __DIR__ . '/Knowledge/KnowledgeDistiller.php';
        
        // Load the 500-Idea Sub-Universe
        require_once __DIR__ . '/SubUniverse/CosmicReasoning.php';
        require_once __DIR__ . '/SubUniverse/LinguisticGalaxy.php';
        require_once __DIR__ . '/SubUniverse/DigitalArchitect.php';
        require_once __DIR__ . '/SubUniverse/InfiniteMemory.php';
        require_once __DIR__ . '/SubUniverse/CreativeMultiverse.php';

        $this->mha = new Attention\MultiHeadAttention(4, 32);
        $this->cot = new Logic\ChainOfThought();
        $this->filter = new Response\ResponseFilter();
        $this->moodEngine = new Emotion\NeuralMoodEngine();
        $this->slangBridge = new Language\HinglishSlangBridge();
        $this->genMemory = new Memory\GenerativeMemory();
        $this->creativity = new Creativity\RandomWalkGenerator(0.8);
        $this->factChecker = new Fact\FactChecker();
        $this->dialogue = new Dialogue\DialogueManager();
        $this->distiller = new Knowledge\KnowledgeDistiller();
        
        // Initialize Multiverse
        $this->cosmic = new SubUniverse\CosmicReasoning();
        $this->galaxy = new SubUniverse\LinguisticGalaxy();
        $this->architect = new SubUniverse\DigitalArchitect();
        $this->infiniteMem = new SubUniverse\InfiniteMemory();
        $this->multiverse = new SubUniverse\CreativeMultiverse();
    }

    public function generate(string $prompt, array $evidence = [], array $analysis = []): string {
        // 1. Multiverse Reasoning
        $this->cosmic->reason($prompt);
        $this->infiniteMem->recall($prompt);
        
        // 2. Standard Generation Pipeline
        $this->cot->reason($prompt, $evidence);
        $distilled = $this->distiller->distill($evidence);
        
        $raw = (!empty($distilled)) ? $this->composeFromEvidence($prompt, $distilled, $analysis) : $this->markov->generateFromPrompt($prompt, 30);
        
        // 3. Artistic Synthesis & Swag
        $swagResponse = $this->galaxy->injectSwag($raw);
        $creativeResponse = $this->multiverse->synthesize($swagResponse);
        
        // 4. Final Polish
        $final = $this->filter->polish($raw);
        $final = $this->slangBridge->inject($final);
        return $this->moodEngine->adapt($final, $analysis['emotion'] ?? 'neutral');
    }

    private function composeFromEvidence(string $prompt, array $evidence, array $analysis): string {
        $tokens = $analysis['processed_prompt']['tokens'] ?? $this->guard->extractKeywords($prompt);
        $best = $this->creativity->diversify($evidence);
        return $this->rewriteForPrompt($prompt, $this->truncateToSentences($best, 2), $tokens);
    }

    private function rewriteForPrompt(string $prompt, string $text, array $tokens): string {
        $focus = $this->pickFocusToken($tokens, explode(' ', $prompt));
        if ($focus && preg_match('/(what|kya|define)/i', $prompt) && !str_contains(strtolower($text), strtolower($focus))) {
            return ucfirst($focus) . ' ' . ltrim($text);
        }
        return $text;
    }

    private function truncateToSentences(string $text, int $maxSentences): string {
        $parts = preg_split('/(?<=[.!?])\s+/', trim($text));
        $parts = array_values(array_filter($parts ?: [], fn($part) => trim($part) !== ''));
        return empty($parts) ? trim($text) : trim(implode(' ', array_slice($parts, 0, $maxSentences)));
    }

    private function pickFocusToken(array $tokens, array $attentionTokens): ?string {
        static $blocked = ['what'=>true, 'who'=>true, 'why'=>true, 'how'=>true, 'kya'=>true, 'kaise'=>true, 'meaning'=>true, 'define'=>true, 'hai'=>true, 'h'=>true];
        foreach (array_merge($tokens, $attentionTokens) as $token) {
            $token = strtolower(trim((string)$token));
            if ($token !== '' && !isset($blocked[$token])) return $token;
        }
        return null;
    }
}
