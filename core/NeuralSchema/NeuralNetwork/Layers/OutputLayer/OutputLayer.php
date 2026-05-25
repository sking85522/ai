<?php
namespace Core\NeuralSchema\NeuralNetwork\Layers\OutputLayer;

require_once dirname(__DIR__) . '/HiddenLayers/HiddenLayer.php';

use Core\NeuralSchema\NeuralNetwork\Layers\HiddenLayers\HiddenLayer;

class OutputLayer extends HiddenLayer {
    public function __construct(int $inputSize, int $neuronCount, string $activation = 'sigmoid') {
        parent::__construct($inputSize, $neuronCount, $activation);
    }
}
