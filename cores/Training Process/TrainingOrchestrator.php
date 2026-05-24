<?php
class TrainingOrchestrator
{
    private DataPipeline $pipeline;
    private SimpleTextClassifier $classifier;
    private MetricsSuite $metrics;

    public function __construct(?DataPipeline $pipeline = null, ?SimpleTextClassifier $classifier = null)
    {
        $this->pipeline = $pipeline ?? new DataPipeline();
        $this->classifier = $classifier ?? new SimpleTextClassifier();
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

    /**
     * Incremental training using shard files located in a directory.
     * Each shard is processed line-by-line and fed to the classifier in batches.
     * Returns summary stats.
     *
     * @param string $shardDir
     * @param int $batchSize number of lines per incremental update
     * @param int|null $maxShards limit number of shards to process (null = all)
     * @return array
     */
    public function trainFromShardFiles(string $shardDir, int $batchSize = 1000, ?int $maxShards = null): array
    {
        $files = glob(rtrim($shardDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');
        sort($files);
        if ($maxShards !== null) {
            $files = array_slice($files, 0, $maxShards);
        }

        $totalLines = 0;
        foreach ($files as $file) {
            if (!is_file($file) || !is_readable($file)) {
                continue;
            }
            $handle = fopen($file, 'r');
            if (!$handle) {
                continue;
            }
            $batch = [];
            while (!feof($handle)) {
                $line = fgets($handle);
                if ($line === false) {
                    break;
                }
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $batch[] = ['text' => $line, 'label' => null];
                $totalLines++;

                if (count($batch) >= $batchSize) {
                    $rows = $this->pipeline->buildDataset($batch);
                    $this->classifier->train($rows);
                    $batch = [];
                }
            }
            if ($batch) {
                $rows = $this->pipeline->buildDataset($batch);
                $this->classifier->train($rows);
            }
            fclose($handle);
        }

        return [
            'shards_processed' => count($files),
            'lines_trained' => $totalLines,
        ];
    }
}
