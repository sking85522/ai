<?php
class Backpropagation
{
    public function adjust(NeuralNetwork $network, float $expected, float $actual, float $learningRate = 0.05): float
    {
        $error = $expected - $actual;
        $delta = $error * $learningRate;
        $network->tune($delta);
        return $error;
    }
}
