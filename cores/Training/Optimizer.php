<?php
namespace Core\Training;

abstract class Optimizer {
    protected float $learningRate;

    public function __construct(float $learningRate = 0.01) {
        $this->learningRate = $learningRate;
    }

    abstract public function update(array &$weights, array $gradients): void;
}
