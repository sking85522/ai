<?php
namespace Core\Training\Optimizers;

require_once dirname(__DIR__) . '/Optimizer.php';

use Core\Training\Optimizer;

class SGD extends Optimizer {
    public function update(array &$weights, array $gradients): void {
        foreach ($weights as $i => &$row) {
            if (is_array($row)) {
                foreach ($row as $j => &$weight) {
                    $weight -= $this->learningRate * (float)($gradients[$i][$j] ?? 0);
                }
                unset($weight);
                continue;
            }

            $row -= $this->learningRate * (float)($gradients[$i] ?? 0);
        }
        unset($row);
    }
}
