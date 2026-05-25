<?php
namespace Core\NeuralSchema\NeuralNetwork\Layers\HiddenLayers;

require_once dirname(__DIR__, 2) . '/Neuron/Neuron.php';
require_once dirname(__DIR__, 2) . '/ActivationFunction/Activation.php';

use Core\NeuralSchema\NeuralNetwork\Neuron\Neuron;
use Core\NeuralSchema\NeuralNetwork\ActivationFunction\Activation;

class HiddenLayer {
    private array $neurons = [];
    private string $activation;

    public function __construct(int $inputSize, int $neuronCount, string $activation = 'relu') {
        $this->activation = $activation;
        for ($i = 0; $i < $neuronCount; $i++) {
            $this->neurons[] = new Neuron($inputSize, $activation);
        }
    }

    public function forward(array $inputs): array {
        $outputs = [];
        foreach ($this->neurons as $neuron) {
            $raw = $neuron->activate($inputs);
            $outputs[] = Activation::apply($raw, $this->activation);
        }
        return $outputs;
    }
}
