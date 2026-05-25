<?php
namespace Core\NeuralSchema;

class NetworkGraph {
    private array $nodes = [];
    private array $edges = [];

    public function addNode(string $id, array $data = []): void {
        $this->nodes[$id] = $data;
    }

    public function connect(string $from, string $to, float $weight = 1.0): void {
        $this->edges[] = [
            'from' => $from,
            'to' => $to,
            'weight' => $weight
        ];
    }

    public function getTopology(): array {
        return [
            'nodes' => $this->nodes,
            'edges' => $this->edges
        ];
    }
}
