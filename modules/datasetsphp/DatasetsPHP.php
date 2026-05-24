<?php
namespace DatasetsPHP;

class DatasetsPHP {
    // Main entry point
}

abstract class Dataset {
    abstract public function count(): int;
    abstract public function getItem(int $idx);
}

class TensorDataset extends Dataset {
    private $tensors;

    public function __construct(array ...$tensors) {
        $this->tensors = $tensors;
    }

    public function count(): int {
        return count($this->tensors[0]);
    }

    public function getItem(int $idx) {
        $result = [];
        foreach ($this->tensors as $tensor) {
            $result[] = $tensor[$idx];
        }
        return count($result) === 1 ? $result[0] : $result;
    }
}

class DataLoader {
    private $dataset;
    private $batchSize;
    private $shuffle;
    private $indices;

    public function __construct(Dataset $dataset, int $batchSize = 1, bool $shuffle = false) {
        $this->dataset = $dataset;
        $this->batchSize = $batchSize;
        $this->shuffle = $shuffle;
        $this->indices = range(0, $dataset->count() - 1);
        if ($this->shuffle) {
            shuffle($this->indices);
        }
    }

    public function getBatches(): array {
        $batches = [];
        $currentBatch = [];
        foreach ($this->indices as $idx) {
            $currentBatch[] = $this->dataset->getItem($idx);
            if (count($currentBatch) === $this->batchSize) {
                $batches[] = $currentBatch;
                $currentBatch = [];
            }
        }
        if (!empty($currentBatch)) {
            $batches[] = $currentBatch;
        }
        return $batches;
    }
}
