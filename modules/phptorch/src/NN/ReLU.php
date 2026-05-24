<?php
namespace PHPTorch\NN;

use PHPTorch\Tensor;

class ReLU extends Module {
    public function forward(...$args): Tensor {
        $x = $args[0];
        // Mock ReLU forward
        return $x;
    }
}
