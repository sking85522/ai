<?php
namespace Core\NLP;

use Core\NLP\Tokenizers\NeuralTokenizer;
use Core\NLP\Entities\EntityRecognizer;
use Core\NLP\Intents\IntentMapper;

/**
 * HRITIK AI - ADVANCED NLP PIPELINE
 * 
 * Orchestrates the linguistic analysis flow, including tokenization, 
 * entity extraction, and semantic intent mapping for bilingual (Hinglish) text.
 */
class NLPPipeline {
    
    /** @var NeuralTokenizer Sub-word tokenizer */
    private $tokenizer;
    
    /** @var EntityRecognizer NER module */
    private $entityRecognizer;
    
    /** @var IntentMapper Semantic intent engine */
    private $intentMapper;

    /**
     * Initializes the NLP pipeline components.
     */
    public function __construct() {
        $this->tokenizer = new NeuralTokenizer();
        $this->entityRecognizer = new EntityRecognizer();
        $this->intentMapper = new IntentMapper();
    }

    /**
     * Processes a raw prompt and returns a structured linguistic analysis.
     * 
     * @param string $text Raw user input
     * @return array {
     *   original: string,
     *   tokens: array,
     *   entities: array,
     *   intent: string,
     *   timestamp: int
     * }
     */
    public function process(string $text): array {
        $cleanText = trim($text);
        
        return [
            'original' => $text,
            'tokens' => $this->tokenizer->tokenize($cleanText),
            'entities' => $this->entityRecognizer->extract($cleanText),
            'intent' => $this->intentMapper->map($cleanText),
            'timestamp' => time()
        ];
    }
}
