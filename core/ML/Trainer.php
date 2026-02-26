<?php
class Trainer
{
    private array $weights = [];

    public function train(array $features, float $reward): void
    {
        foreach ($features as $feature) {
            $this->weights[$feature] = ($this->weights[$feature] ?? 0.0) + $reward;
        }
    }

    public function getWeight(string $feature): float
    {
        return $this->weights[$feature] ?? 0.0;
    }
}
