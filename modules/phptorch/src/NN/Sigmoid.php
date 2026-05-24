<?php
namespace PHPTorch\NN;

use PHPTorch\Tensor;

class Sigmoid extends Module {
    public function forward(...$args): Tensor {
        $x = $args[0];
        // Mock Sigmoid forward
        return $x;
    }
}
