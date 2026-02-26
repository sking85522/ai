<?php
class DomainUpgradeGateway
{
    private ?NeuralSchemaAssistant $neural = null;
    private ?DataHandlingAssistant $data = null;
    private ?MachineLearningAssistant $ml = null;
    private ?TrainingProcessAssistant $train = null;
    private ?EvaluationAssistant $eval = null;
    private ?ComputerVisionAssistant $cv = null;
    private ?SpeechProcessingAssistant $speech = null;
    private ?GenerativeAIAssistant $gen = null;
    private ?ReinforcementLearningAssistant $rl = null;
    private ?MatrixOps $matrix = null;
    private ?TrainingOrchestrator $trainingOrchestrator = null;

    public function __construct()
    {
        $this->neural = class_exists('NeuralSchemaAssistant') ? new NeuralSchemaAssistant() : null;
        $this->data = class_exists('DataHandlingAssistant') ? new DataHandlingAssistant() : null;
        $this->ml = class_exists('MachineLearningAssistant') ? new MachineLearningAssistant() : null;
        $this->train = class_exists('TrainingProcessAssistant') ? new TrainingProcessAssistant() : null;
        $this->eval = class_exists('EvaluationAssistant') ? new EvaluationAssistant() : null;
        $this->cv = class_exists('ComputerVisionAssistant') ? new ComputerVisionAssistant() : null;
        $this->speech = class_exists('SpeechProcessingAssistant') ? new SpeechProcessingAssistant() : null;
        $this->gen = class_exists('GenerativeAIAssistant') ? new GenerativeAIAssistant() : null;
        $this->rl = class_exists('ReinforcementLearningAssistant') ? new ReinforcementLearningAssistant() : null;
        $this->matrix = class_exists('MatrixOps') ? new MatrixOps() : null;
        $this->trainingOrchestrator = class_exists('TrainingOrchestrator') ? new TrainingOrchestrator() : null;
    }

    public function handle(string $input, string $language = 'bilingual'): ?string
    {
        $x = mb_strtolower($input, 'UTF-8');
        if ($this->matrix !== null) {
            $res = $this->matrix->trySolve($input);
            if ($res !== null) {
                return $res;
            }
        }

        if (str_contains($x, 'neural schema')) {
            return $this->neural?->describe($language);
        }
        if (str_contains($x, 'data handling')) {
            return $this->data?->describe($language);
        }
        if (str_contains($x, 'machine learning algorithms')) {
            return $this->ml?->describe($language);
        }
        if (str_contains($x, 'training process')) {
            return $this->train?->describe($language);
        }
        if (str_contains($x, 'evaluation')) {
            return $this->eval?->describe($language);
        }
        if (str_contains($x, 'computer vision')) {
            return $this->cv?->describe($language);
        }
        if (str_contains($x, 'speech processing')) {
            return $this->speech?->describe($language);
        }
        if (str_contains($x, 'generative ai')) {
            return $this->gen?->describe($language);
        }
        if (str_contains($x, 'reinforcement learning')) {
            return $this->rl?->describe($language);
        }
        if (str_contains($x, 'run training demo') || str_contains($x, 'training demo')) {
            if ($this->trainingOrchestrator === null) {
                return $language === 'hi' ? 'Training orchestrator load nahi hua.' : 'Training orchestrator is not loaded.';
            }
            $report = $this->trainingOrchestrator->runDemo();
            $m = $report['metrics'] ?? [];
            return 'Training Demo: train=' . ($report['train_size'] ?? 0)
                . ', test=' . ($report['test_size'] ?? 0)
                . ', acc=' . ($m['accuracy'] ?? 0)
                . ', precision=' . ($m['precision'] ?? 0)
                . ', recall=' . ($m['recall'] ?? 0)
                . ', f1=' . ($m['f1'] ?? 0);
        }

        if (str_contains($x, 'core folders') || str_contains($x, 'modules added')) {
            $parts = array_filter([
                $this->neural?->describe($language),
                $this->data?->describe($language),
                $this->ml?->describe($language),
                $this->train?->describe($language),
                $this->eval?->describe($language),
                $this->cv?->describe($language),
                $this->speech?->describe($language),
                $this->gen?->describe($language),
                $this->rl?->describe($language),
            ]);
            return implode(' ', $parts);
        }

        return null;
    }
}
