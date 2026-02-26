<?php
class TrainingOrchestrator
{
    private DataPipeline $pipeline;
    private SimpleTextClassifier $classifier;
    private MetricsSuite $metrics;

    public function __construct()
    {
        $this->pipeline = new DataPipeline();
        $this->classifier = new SimpleTextClassifier();
        $this->metrics = new MetricsSuite();
    }

    public function runDemo(): array
    {
        $samples = [
            ['text' => 'capital of india', 'label' => 'geo'],
            ['text' => 'capital of japan', 'label' => 'geo'],
            ['text' => 'solve sin 30', 'label' => 'math'],
            ['text' => '2x + 4 = 10', 'label' => 'math'],
            ['text' => 'write php function', 'label' => 'code'],
            ['text' => 'java class example', 'label' => 'code'],
            ['text' => 'make quiz physics', 'label' => 'quiz'],
            ['text' => 'math quiz banao', 'label' => 'quiz'],
        ];

        $rows = $this->pipeline->buildDataset($samples);
        $split = $this->pipeline->splitTrainTest($rows, 0.75);
        $this->classifier->train($split['train']);

        $truth = [];
        $pred = [];
        foreach ($split['test'] as $row) {
            $truth[] = (string) $row['label'];
            $pred[] = $this->classifier->predict((array) ($row['tokens'] ?? []));
        }
        $metrics = $this->metrics->evaluateClassification($truth, $pred);

        return [
            'train_size' => count($split['train']),
            'test_size' => count($split['test']),
            'metrics' => $metrics,
        ];
    }
}
