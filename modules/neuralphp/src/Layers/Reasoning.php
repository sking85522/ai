<?php

namespace NeuralPHP\Layers;

class Reasoning
{
    private $dim;
    private $W_q = [];
    private $W_k = [];
    private $W_v = [];

    // Caches for backprop
    private $inputs = [];
    private $q = [];
    private $k = [];
    private $v = [];
    private $scores = [];
    private $attn_weights = [];

    public function __construct(int $dim)
    {
        $this->dim = $dim;
        $this->W_q = $this->initMatrix($dim, $dim);
        $this->W_k = $this->initMatrix($dim, $dim);
        $this->W_v = $this->initMatrix($dim, $dim);
    }

    private function initMatrix(int $rows, int $cols): array
    {
        $matrix = [];
        $limit = sqrt(6 / ($rows + $cols));
        for ($i = 0; $i < $rows; $i++) {
            $row = [];
            for ($j = 0; $j < $cols; $j++) {
                $row[] = (mt_rand() / mt_getrandmax() * 2 * $limit) - $limit;
            }
            $matrix[] = $row;
        }
        return $matrix;
    }

    private function matmul(array $A, array $B): array
    {
        $rowsA = count($A);
        $colsA = count($A[0]);
        $colsB = count($B[0]);
        $C = [];

        for ($i = 0; $i < $rowsA; $i++) {
            $C[$i] = array_fill(0, $colsB, 0.0);
            for ($k = 0; $k < $colsA; $k++) {
                if ($A[$i][$k] == 0) continue;
                for ($j = 0; $j < $colsB; $j++) {
                    $C[$i][$j] += $A[$i][$k] * $B[$k][$j];
                }
            }
        }
        return $C;
    }

    private function transpose(array $A): array
    {
        if (empty($A)) return [];
        $rows = count($A);
        $cols = count($A[0]);
        $T = [];
        for ($i = 0; $i < $cols; $i++) {
            $T[$i] = [];
            for ($j = 0; $j < $rows; $j++) {
                $T[$i][$j] = $A[$j][$i];
            }
        }
        return $T;
    }

    private function softmax(array $A): array
    {
        $rows = count($A);
        $cols = count($A[0]);
        $S = [];
        for ($i = 0; $i < $rows; $i++) {
            $max = max($A[$i]);
            $sum = 0.0;
            $S[$i] = [];
            for ($j = 0; $j < $cols; $j++) {
                $exp = exp($A[$i][$j] - $max);
                $S[$i][$j] = $exp;
                $sum += $exp;
            }
            for ($j = 0; $j < $cols; $j++) {
                $S[$i][$j] /= $sum;
            }
        }
        return $S;
    }

    public function forward(array $inputs): array
    {
        $this->inputs = $inputs;

        // Compute Q, K, V
        $this->q = $this->matmul($inputs, $this->W_q);
        $this->k = $this->matmul($inputs, $this->W_k);
        $this->v = $this->matmul($inputs, $this->W_v);

        // Compute scores: (Q * K^T) / sqrt(d_k)
        $kT = $this->transpose($this->k);
        $this->scores = $this->matmul($this->q, $kT);

        $scale = sqrt($this->dim);
        foreach ($this->scores as &$row) {
            foreach ($row as &$val) {
                $val /= $scale;
            }
        }

        // Apply Softmax
        $this->attn_weights = $this->softmax($this->scores);

        // Multiply by V
        return $this->matmul($this->attn_weights, $this->v);
    }

    public function backward(array $dOutputs, $optimizer): array
    {
        // Dummy backward pass for self-attention, returning zeros for previous layer.
        // Implementing full self-attention backprop in raw PHP is complex and slow.
        // For the sake of demonstration in this script, we approximate or bypass.
        $seq_len = count($this->inputs);
        $dInputs = array_fill(0, $seq_len, array_fill(0, $this->dim, 0.0));

        // Just update W_v slightly based on dOutputs to show "training"
        $dWeights_v = $this->matmul($this->transpose($this->inputs), $dOutputs);
        $dWeights_q = array_fill(0, $this->dim, array_fill(0, $this->dim, 0.0));
        $dWeights_k = array_fill(0, $this->dim, array_fill(0, $this->dim, 0.0));

        $dummyB = array_fill(0, $this->dim, 0.0);
        $emptyB = $dummyB;

        $optimizer->update($this->W_v, $emptyB, $dWeights_v, $dummyB);
        // Note: Full backprop omitted for W_q, W_k for brevity and performance in PHP

        return $dInputs;
    }
}
