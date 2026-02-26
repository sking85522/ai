<?php
class Layer
{
    private array $weights;
    private array $biases;
    private string $activation;

    public function __construct(int $inputSize, int $outputSize, string $activation = 'relu')
    {
        $this->activation = $activation;
        $this->weights = [];
        $this->biases = array_fill(0, $outputSize, 0.1);

        for ($o = 0; $o < $outputSize; $o++) {
            $row = [];
            for ($i = 0; $i < $inputSize; $i++) {
                $row[] = (($o + $i + 1) % 7) / 10;
            }
            $this->weights[] = $row;
        }
    }

    public function forward(array $inputs): array
    {
        $outputs = [];
        foreach ($this->weights as $outIndex => $row) {
            $sum = $this->biases[$outIndex];
            foreach ($row as $inIndex => $weight) {
                $sum += ($inputs[$inIndex] ?? 0.0) * $weight;
            }
            $outputs[] = $this->activation === 'sigmoid'
                ? Activation::sigmoid($sum)
                : Activation::relu($sum);
        }
        return $outputs;
    }

    public function adjust(float $delta): void
    {
        foreach ($this->weights as &$row) {
            foreach ($row as &$weight) {
                $weight += $delta;
            }
        }
        unset($row, $weight);
    }
}
