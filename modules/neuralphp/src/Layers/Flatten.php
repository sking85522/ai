<?php

namespace NeuralPHP\Layers;

class Flatten
{
    private $input_shape = [];

    public function forward(array $inputs): array
    {
        $this->input_shape = [count($inputs), count($inputs[0] ?? [])];
        $flattened = [];
        foreach ($inputs as $row) {
            if (is_array($row)) {
                foreach ($row as $val) {
                    $flattened[] = $val;
                }
            } else {
                $flattened[] = $row;
            }
        }
        return $flattened;
    }

    public function backward(array $dOutputs, $optimizer): array
    {
        $dInputs = [];
        $idx = 0;
        for ($i = 0; $i < $this->input_shape[0]; $i++) {
            $row = [];
            for ($j = 0; $j < $this->input_shape[1]; $j++) {
                $row[] = $dOutputs[$idx++] ?? 0.0;
            }
            $dInputs[] = $row;
        }
        return $dInputs;
    }
}
