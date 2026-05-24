<?php
namespace Core\GenerativeAI;

require_once __DIR__ . '/MarkovGenerator.php';
require_once __DIR__ . '/Transformers.php';
require_once __DIR__ . '/LocalLanguageModel.php';

/**
 * HRITIK AI - GENERATIVE AI ASSISTANT (PRO)
 * Orchestrates deep reasoning, multi-head attention, and neural synthesis.
 */
class GenerativeAIAssistant {
    private MarkovGenerator $markov;
    private Transformers $transformer;
    private LocalLanguageModel $localLanguageModel;

    public function __construct() {
        $this->markov = new MarkovGenerator();
        $this->transformer = new Transformers();
        
        // Bootstrapping the Neural Brain with the Corpus
        $corpusPath = __DIR__ . '/neural_corpus.txt';
        if (file_exists($corpusPath)) {
            $lines = file($corpusPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $this->markov->learn($line);
            }
        }

        // ResponseQualityGuard is outside, so we use its full namespace
        $guard = new \Core\Response\ResponseQualityGuard();
        
        // LocalLanguageModel handles the internal sub-modules (Attention, Logic, Response)
        $this->localLanguageModel = new LocalLanguageModel($this->markov, $this->transformer, $guard);
    }

    public function generateThought(string $prompt = "", array $context = [], array $analysis = []): string {
        return $this->localLanguageModel->generate($prompt, $context, $analysis);
    }

    public function learn(string $text): void {
        $this->markov->learn($text);
    }

    public function getMarkov(): MarkovGenerator {
        return $this->markov;
    }
}
