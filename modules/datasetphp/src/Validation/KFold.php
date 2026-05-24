<?php
namespace DatasetPHP\Validation;

/**
 * K-Fold Cross-Validation — Splits data into K folds for robust model evaluation.
 */
class KFold
{
    private $k;
    private $shuffle;

    public function __construct(int $k = 5, bool $shuffle = true)
    {
        $this->k = $k;
        $this->shuffle = $shuffle;
    }

    /**
     * Generate train/test index splits.
     * @param array $X Feature data
     * @return array of ['train_indices' => [], 'test_indices' => []]
     */
    public function split(array $X): array
    {
        $n = count($X);
        $indices = range(0, $n - 1);

        if ($this->shuffle) {
            shuffle($indices);
        }

        $foldSize = intval(ceil($n / $this->k));
        $folds = [];

        for ($i = 0; $i < $this->k; $i++) {
            $testStart = $i * $foldSize;
            $testIndices = array_slice($indices, $testStart, $foldSize);
            $trainIndices = array_values(array_diff($indices, $testIndices));

            $folds[] = [
                'train_indices' => $trainIndices,
                'test_indices' => $testIndices,
            ];
        }

        return $folds;
    }

    /**
     * Generate actual train/test data splits.
     */
    public function splitData(array $X, array $y): array
    {
        $folds = $this->split($X);
        $result = [];

        foreach ($folds as $fold) {
            $X_train = []; $y_train = [];
            $X_test = []; $y_test = [];

            foreach ($fold['train_indices'] as $idx) {
                $X_train[] = $X[$idx];
                $y_train[] = $y[$idx];
            }
            foreach ($fold['test_indices'] as $idx) {
                $X_test[] = $X[$idx];
                $y_test[] = $y[$idx];
            }

            $result[] = [
                'X_train' => $X_train, 'y_train' => $y_train,
                'X_test' => $X_test, 'y_test' => $y_test,
            ];
        }

        return $result;
    }
}
