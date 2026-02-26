<?php
class NeuralNetwork
{
    private Layer $hiddenLayer;
    private Layer $outputLayer;

    public function __construct(int $inputSize = 8, int $hiddenSize = 6, int $outputSize = 3)
    {
        $this->hiddenLayer = new Layer($inputSize, $hiddenSize, 'relu');
        $this->outputLayer = new Layer($hiddenSize, $outputSize, 'sigmoid');
    }

    public function forward(array $inputs): array
    {
        $hidden = $this->hiddenLayer->forward($inputs);
        return $this->outputLayer->forward($hidden);
    }

    public function score(array $inputs): float
    {
        $outputs = $this->forward($inputs);
        return round(array_sum($outputs) / max(1, count($outputs)), 4);
    }

    public function tune(float $delta): void
    {
        $this->hiddenLayer->adjust($delta);
        $this->outputLayer->adjust($delta / 2);
    }
}
