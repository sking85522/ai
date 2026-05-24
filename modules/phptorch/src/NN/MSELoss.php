<?php
namespace PHPTorch\NN;

use PHPTorch\Tensor;

class MSELoss extends Module {
    public function forward(...$args): Tensor {
        $input = $args[0];
        $target = $args[1] ?? null;
        // Mock MSE calculation
        return new Tensor(0.5, [$input, $target], 'MSE');
    }
}
