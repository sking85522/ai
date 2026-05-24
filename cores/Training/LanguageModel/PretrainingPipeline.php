<?php
namespace Core\Training\LanguageModel;

require_once __DIR__ . '/CorpusBuilder.php';
require_once __DIR__ . '/Tokenizer.php';
require_once __DIR__ . '/NGramLanguageModel.php';

class PretrainingPipeline {
    private CorpusBuilder $corpusBuilder;
    private Tokenizer $tokenizer;

    public function __construct() {
        $this->corpusBuilder = new CorpusBuilder();
        $this->tokenizer = new Tokenizer();
    }

    public function pretrainFromPairs(array $pairs, int $order = 3): array {
        $documents = $this->corpusBuilder->buildFromPairs($pairs);
        return $this->pretrainDocuments($documents, $order);
    }

    public function pretrainDocuments(array $documents, int $order = 3): array {
        $model = new NGramLanguageModel($order);
        $tokenCount = 0;

        foreach ($documents as $document) {
            $tokens = $this->tokenizer->tokenize($document);
            $tokenCount += count($tokens);
            $model->observe($tokens);
        }

        return [
            'documents' => count($documents),
            'tokens' => $tokenCount,
            'model' => $model->export(),
        ];
    }

    public function saveModel(array $modelData, string $path): void {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($path, json_encode($modelData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
