<?php
namespace Core\ML;

require_once dirname(__DIR__) . '/Matrix/MatrixOps.php';
use Core\Matrix\MatrixOps;

/**
 * HRITIK AI - LINEAR REGRESSION
 * Ordinary Least Squares implementation using Matrix Mathematics.
 */
class LinearRegression {
    private ?\NumPHP\Core\NDArray $weights = null;

    /**
     * Train the model using OLS formula: w = (X^T * X)^-1 * X^T * y
     */
    public function fit(array $X, array $y): void {
        $xMatrix = MatrixOps::create($X);
        
        $yCol = [];
        foreach($y as $val) $yCol[] = [$val];
        $yMatrix = MatrixOps::create($yCol);

        $xT = MatrixOps::transpose($xMatrix);
        $xTx = MatrixOps::dot($xT, $xMatrix);
        $xTx_inv = MatrixOps::inverse($xTx);
        $xTx_inv_xT = MatrixOps::dot($xTx_inv, $xT);
        
        $this->weights = MatrixOps::dot($xTx_inv_xT, $yMatrix);
    }

    /**
     * Predict continuous values
     */
    public function predict(array $X): array {
        if ($this->weights === null) throw new \Exception("Model not trained.");

        $xMatrix = MatrixOps::create($X);
        $preds = MatrixOps::dot($xMatrix, $this->weights);
        $rawPreds = $preds->getData(); 
        
        $flattened = [];
        foreach ($rawPreds as $row) {
            $flattened[] = is_array($row) ? $row[0] : $row;
        }
        return $flattened;
    }

    public function getWeights(): array {
        if ($this->weights === null) {
            return [];
        }

        $data = $this->weights->getData();
        $weights = [];
        foreach ($data as $row) {
            $weights[] = is_array($row) ? ($row[0] ?? 0) : $row;
        }

        return $weights;
    }
}
