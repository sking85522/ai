<?php
namespace PHPTorch\NN;

use PHPTorch\Tensor;

class Linear extends Module {
    public $weight;
    public $bias;

    public function __construct(int $inFeatures, int $outFeatures, bool $bias = true) {
        // Initialize weights randomly
        $wData = [];
        for ($i = 0; $i < $outFeatures; $i++) {
            $row = [];
            for ($j = 0; $j < $inFeatures; $j++) {
                $row[] = (mt_rand(-100, 100) / 1000.0);
            }
            $wData[] = $row;
        }
        $this->weight = new Tensor($wData, [], '', true);

        if ($bias) {
            $bData = array_fill(0, $outFeatures, 0.0);
            $this->bias = new Tensor($bData, [], '', true);
        } else {
            $this->bias = null;
        }
    }

    public function forward(...$args): Tensor {
        $x = $args[0];
        // Mock linear transformation: x * W^T + b
        // Returning $x directly for simplified mock graph
        return $x;
    }
}
