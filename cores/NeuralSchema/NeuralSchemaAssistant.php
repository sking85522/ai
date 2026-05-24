<?php
namespace Core\NeuralSchema;

require_once __DIR__ . '/NeuralNetwork/Layers/InputLayer/InputLayer.php';
require_once __DIR__ . '/NeuralNetwork/Layers/HiddenLayers/HiddenLayer.php';
require_once __DIR__ . '/NeuralNetwork/Layers/OutputLayer/OutputLayer.php';
require_once __DIR__ . '/NetworkGraph.php';

use Core\NeuralSchema\NeuralNetwork\Layers\InputLayer\InputLayer;
use Core\NeuralSchema\NeuralNetwork\Layers\HiddenLayers\HiddenLayer;
use Core\NeuralSchema\NeuralNetwork\Layers\OutputLayer\OutputLayer;

class NeuralSchemaAssistant {
    private InputLayer $input;
    private array $hidden = [];
    private OutputLayer $output;
    private NetworkGraph $graph;

    public function __construct(int $inputSize, array $hiddenSizes, int $outputSize) {
        $this->graph = new NetworkGraph();
        
        // 1. Initialize Layers
        $this->input = new InputLayer($inputSize);
        
        $lastSize = $inputSize;
        foreach ($hiddenSizes as $idx => $size) {
            $this->hidden[] = new HiddenLayer($lastSize, $size);
            $lastSize = $size;
        }

        $this->output = new OutputLayer($lastSize, $outputSize);
    }

    /**
     * Executes a full forward pass through the schema.
     */
    public function forwardPass(array $data): array {
        $current = $this->input->forward($data);
        
        foreach ($this->hidden as $layer) {
            $current = $layer->forward($current);
        }
        
        return $this->output->forward($current);
    }

    public function getSchemaSummary(): string {
        return "Neural Schema Initialized: Layers=[" . (count($this->hidden) + 2) . "], Graph Nodes mapped.";
    }
}
