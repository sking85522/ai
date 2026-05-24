<?php
namespace Core\DL\Recurrent;

require_once dirname(__DIR__) . '/Tensor.php';
use Core\DL\Tensor;

/**
 * HRITIK AI - REAL LSTM CELL (Long Short-Term Memory)
 *
 * Implements the full LSTM equations with 4 gates:
 *   f_t = σ(W_f · [h_{t-1}, x_t] + b_f)        ← Forget gate
 *   i_t = σ(W_i · [h_{t-1}, x_t] + b_i)        ← Input gate
 *   g_t = tanh(W_g · [h_{t-1}, x_t] + b_g)     ← Candidate cell
 *   o_t = σ(W_o · [h_{t-1}, x_t] + b_o)        ← Output gate
 *   c_t = f_t ⊙ c_{t-1} + i_t ⊙ g_t           ← Cell state
 *   h_t = o_t ⊙ tanh(c_t)                       ← Hidden output
 *
 * Weight matrix W has shape (4*hiddenSize × (inputSize + hiddenSize))
 * This combines all 4 gate weight matrices into one for efficient matmul.
 *
 * Supports full BPTT (Backpropagation Through Time) for training.
 *
 * Parameters per cell: 4 × (inputSize + hiddenSize) × hiddenSize + 4 × hiddenSize
 */
class LSTMCell {

    /** @var Tensor Combined weight matrix for all 4 gates (4H × (I+H)) */
    public Tensor $W;

    /** @var float[] Combined bias for all 4 gates (4H) */
    public array $b;

    /** @var Tensor Gradient accumulator for weights */
    public Tensor $dW;

    /** @var float[] Gradient accumulator for bias */
    public array $db;

    private int $inputSize;
    private int $hiddenSize;

    /** @var array Cache of intermediate values per timestep for backward pass */
    private array $cache = [];

    /**
     * @param int $inputSize  Dimension of input vectors
     * @param int $hiddenSize Dimension of hidden state
     */
    public function __construct(int $inputSize, int $hiddenSize) {
        $this->inputSize = $inputSize;
        $this->hiddenSize = $hiddenSize;

        $combinedSize = $inputSize + $hiddenSize;
        $gateSize = 4 * $hiddenSize;

        // Xavier initialization for all gate weights combined
        $this->W = Tensor::xavierInit($gateSize, $combinedSize);
        $this->b = array_fill(0, $gateSize, 0.0);

        // Initialize forget gate bias to 1.0 (helps learning long-term dependencies)
        for ($i = 0; $i < $hiddenSize; $i++) {
            $this->b[$i] = 1.0;
        }

        // Gradient accumulators
        $this->dW = Tensor::zeros($gateSize, $combinedSize);
        $this->db = array_fill(0, $gateSize, 0.0);
    }

    /**
     * Forward pass for a single timestep.
     *
     * @param float[] $x     Input vector (length inputSize)
     * @param float[] $hPrev Previous hidden state (length hiddenSize)
     * @param float[] $cPrev Previous cell state (length hiddenSize)
     * @param int     $t     Timestep index (for caching)
     * @return array [h_new, c_new] — new hidden and cell states
     */
    public function step(array $x, array $hPrev, array $cPrev, int $t = 0): array {
        $H = $this->hiddenSize;

        // 1. Concatenate: combined = [h_{t-1}, x_t]
        $combined = array_merge($hPrev, $x);

        // 2. Linear: gates_raw = W @ combined + b
        $gatesRaw = $this->W->matvec($combined);
        $gateSize = 4 * $H;
        for ($k = 0; $k < $gateSize; $k++) {
            $gatesRaw[$k] += $this->b[$k];
        }

        // 3. Split into 4 gates and apply activations
        $f = [];  // Forget gate (sigmoid)
        $i = [];  // Input gate (sigmoid)
        $g = [];  // Candidate (tanh)
        $o = [];  // Output gate (sigmoid)

        for ($k = 0; $k < $H; $k++) {
            // Clamp for numerical stability
            $fRaw = max(-500.0, min(500.0, $gatesRaw[$k]));
            $iRaw = max(-500.0, min(500.0, $gatesRaw[$H + $k]));
            $gRaw = $gatesRaw[2 * $H + $k];
            $oRaw = max(-500.0, min(500.0, $gatesRaw[3 * $H + $k]));

            $f[$k] = 1.0 / (1.0 + exp(-$fRaw));    // σ
            $i[$k] = 1.0 / (1.0 + exp(-$iRaw));    // σ
            $g[$k] = tanh($gRaw);                    // tanh
            $o[$k] = 1.0 / (1.0 + exp(-$oRaw));    // σ
        }

        // 4. Cell state update: c_t = f ⊙ c_{t-1} + i ⊙ g
        $c = [];
        for ($k = 0; $k < $H; $k++) {
            $c[$k] = $f[$k] * $cPrev[$k] + $i[$k] * $g[$k];
        }

        // 5. Hidden state: h_t = o ⊙ tanh(c_t)
        $tanhC = array_map('tanh', $c);
        $h = [];
        for ($k = 0; $k < $H; $k++) {
            $h[$k] = $o[$k] * $tanhC[$k];
        }

        // 6. Cache for backward pass
        $this->cache[$t] = [
            'combined' => $combined,
            'f' => $f,
            'i' => $i,
            'g' => $g,
            'o' => $o,
            'c' => $c,
            'cPrev' => $cPrev,
            'tanhC' => $tanhC,
        ];

        return [$h, $c];
    }

    /**
     * Backward pass for a single timestep (BPTT step).
     *
     * @param float[] $dh     Gradient of loss w.r.t. hidden state h_t
     * @param float[] $dcNext Gradient flowing back from c_{t+1} (from the next timestep)
     * @param int     $t      Timestep index
     * @return array [dh_prev, dc_prev, dx] — gradients for previous hidden, cell, and input
     */
    public function backward(array $dh, array $dcNext, int $t): array {
        $H = $this->hiddenSize;
        $cache = $this->cache[$t];

        $f = $cache['f'];
        $i = $cache['i'];
        $g = $cache['g'];
        $o = $cache['o'];
        $c = $cache['c'];
        $cPrev = $cache['cPrev'];
        $tanhC = $cache['tanhC'];
        $combined = $cache['combined'];

        // Gradient to cell state:
        // dc = dh ⊙ o ⊙ (1 - tanh²(c)) + dc_next
        $dc = [];
        for ($k = 0; $k < $H; $k++) {
            $tc = $tanhC[$k];
            $dc[$k] = $dh[$k] * $o[$k] * (1.0 - $tc * $tc) + $dcNext[$k];
        }

        // Gate gradients (pre-activation):
        // df_raw = dc ⊙ c_{t-1} ⊙ f ⊙ (1-f)     — forget gate
        // di_raw = dc ⊙ g ⊙ i ⊙ (1-i)            — input gate
        // dg_raw = dc ⊙ i ⊙ (1-g²)               — candidate
        // do_raw = dh ⊙ tanh(c) ⊙ o ⊙ (1-o)      — output gate
        $dgates = [];
        for ($k = 0; $k < $H; $k++) {
            $dgates[$k]          = $dc[$k] * $cPrev[$k] * $f[$k] * (1.0 - $f[$k]);
            $dgates[$H + $k]     = $dc[$k] * $g[$k]     * $i[$k] * (1.0 - $i[$k]);
            $dgates[2*$H + $k]   = $dc[$k] * $i[$k]     * (1.0 - $g[$k] * $g[$k]);
            $dgates[3*$H + $k]   = $dh[$k] * $tanhC[$k] * $o[$k] * (1.0 - $o[$k]);
        }

        // Accumulate weight gradients: dW += outer(dgates, combined)
        $this->dW->addOuterProduct($dgates, $combined);

        // Accumulate bias gradients: db += dgates
        $gateSize = 4 * $H;
        for ($k = 0; $k < $gateSize; $k++) {
            $this->db[$k] += $dgates[$k];
        }

        // Gradient through input: dcombined = W^T @ dgates
        $dcombined = $this->W->transposeMulVec($dgates);

        // Split dcombined into dh_prev and dx
        $dhPrev = array_slice($dcombined, 0, $H);
        $dx = array_slice($dcombined, $H);

        // Cell state gradient to previous timestep: dc_{t-1} = dc ⊙ f
        $dcPrev = [];
        for ($k = 0; $k < $H; $k++) {
            $dcPrev[$k] = $dc[$k] * $f[$k];
        }

        return [$dhPrev, $dcPrev, $dx];
    }

    /**
     * Reset gradient accumulators to zero.
     */
    public function zeroGrad(): void {
        $this->dW->zero();
        $this->db = array_fill(0, 4 * $this->hiddenSize, 0.0);
    }

    /**
     * Clear cached forward pass data (free memory after training step).
     */
    public function clearCache(): void {
        $this->cache = [];
    }

    /**
     * Get total parameter count for this cell.
     */
    public function paramCount(): int {
        $combinedSize = $this->inputSize + $this->hiddenSize;
        return (4 * $this->hiddenSize * $combinedSize) + (4 * $this->hiddenSize);
    }

    /**
     * Export weights for serialization.
     */
    public function exportWeights(): array {
        return [
            'type' => 'lstm_cell',
            'input_size' => $this->inputSize,
            'hidden_size' => $this->hiddenSize,
            'W' => $this->W->data,
            'b' => $this->b,
        ];
    }

    /**
     * Import weights from serialized data.
     */
    public function importWeights(array $saved): void {
        if (isset($saved['W'])) {
            $this->W->data = $saved['W'];
        }
        if (isset($saved['b'])) {
            $this->b = $saved['b'];
        }
    }

    /**
     * Get the hidden size.
     */
    public function getHiddenSize(): int {
        return $this->hiddenSize;
    }

    /**
     * Get the input size.
     */
    public function getInputSize(): int {
        return $this->inputSize;
    }
}
