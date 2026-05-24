<?php
namespace Core\ML;

require_once __DIR__ . '/LinearRegression.php';

class Predictor {
    private LinearRegression $lr;

    public function __construct() {
        $this->lr = new LinearRegression();
    }

    /**
     * High-level prediction API.
     */
    public function predictOutcome(array $features): array {
        try {
            return $this->lr->predict($features);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
