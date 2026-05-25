<?php
namespace Core\GenerativeAI;

require_once dirname(__DIR__) . '/DL/Tensor.php';
require_once dirname(__DIR__) . '/DL/Embedding.php';
require_once dirname(__DIR__) . '/DL/LinearLayer.php';
require_once dirname(__DIR__) . '/DL/Recurrent/LSTMCell.php';
require_once dirname(__DIR__) . '/DL/Vocabulary.php';
require_once dirname(__DIR__) . '/DL/ModelSerializer.php';
require_once dirname(__DIR__) . '/DL/Loss/LossFunctions.php';
require_once dirname(__DIR__) . '/DL/Optimizers/AdamOptimizer.php';

use Core\DL\Tensor;
use Core\DL\Embedding;
use Core\DL\LinearLayer;
use Core\DL\Recurrent\LSTMCell;
use Core\DL\Vocabulary;
use Core\DL\ModelSerializer;
use Core\DL\Loss\LossFunctions;
use Core\DL\Optimizers\AdamOptimizer;

/**
 * HRITIK AI - LSTM LANGUAGE MODEL
 *
 * A real neural language model that generates text by predicting the next word.
 *
 * Architecture:
 *   Input (word index) → Embedding(V, E) → LSTM1(E, H) → LSTM2(H, H) → Linear(H, V) → Softmax
 *
 * Where:
 *   V = vocabulary size (default 8000)
 *   E = embedding dimension (default 64)
 *   H = hidden size (default 128)
 *
 * Total Parameters: ~2M
 *   Embedding:  V × E       = 512,000
 *   LSTM1:      4(E+H)×H+4H = 98,816
 *   LSTM2:      4(H+H)×H+4H = 131,584
 *   Output:     H × V + V   = 1,032,000
 *   ─────────────────────────────────────
 *   Total:                   ≈ 1,774,400
 */
class LSTMLanguageModel {

    private Embedding $embedding;
    private LSTMCell $lstm1;
    private LSTMCell $lstm2;
    private LinearLayer $outputLayer;

    private int $vocabSize;
    private int $embeddingDim;
    private int $hiddenSize;

    /** @var array Cached token IDs from last forward pass (for backward) */
    private array $lastTokenIds = [];
    private int $lastSeqLen = 0;

    /**
     * @param int $vocabSize    Size of vocabulary (V)
     * @param int $embeddingDim Embedding vector dimension (E)
     * @param int $hiddenSize   LSTM hidden state dimension (H)
     */
    public function __construct(int $vocabSize = 8000, int $embeddingDim = 64, int $hiddenSize = 128) {
        $this->vocabSize = $vocabSize;
        $this->embeddingDim = $embeddingDim;
        $this->hiddenSize = $hiddenSize;

        $this->embedding = new Embedding($vocabSize, $embeddingDim);
        $this->lstm1 = new LSTMCell($embeddingDim, $hiddenSize);
        $this->lstm2 = new LSTMCell($hiddenSize, $hiddenSize);
        $this->outputLayer = new LinearLayer($hiddenSize, $vocabSize);
    }

    /**
     * Forward pass through the entire sequence.
     * Returns logits at each timestep.
     *
     * @param int[] $tokenIds Sequence of token indices
     * @return array Array of logit vectors (one per timestep)
     */
    public function forward(array $tokenIds): array {
        $seqLen = count($tokenIds);
        $H = $this->hiddenSize;

        // Initialize states to zero
        $h1 = Tensor::vecZeros($H);
        $c1 = Tensor::vecZeros($H);
        $h2 = Tensor::vecZeros($H);
        $c2 = Tensor::vecZeros($H);

        $allLogits = [];

        for ($t = 0; $t < $seqLen; $t++) {
            // Embedding lookup
            $emb = $this->embedding->forward($tokenIds[$t]);

            // LSTM Layer 1
            [$h1, $c1] = $this->lstm1->step($emb, $h1, $c1, $t);

            // LSTM Layer 2
            [$h2, $c2] = $this->lstm2->step($h1, $h2, $c2, $t);

            // Output projection
            $logits = $this->outputLayer->forward($h2, $t);

            $allLogits[$t] = $logits;
        }

        // Cache for backward
        $this->lastTokenIds = $tokenIds;
        $this->lastSeqLen = $seqLen;

        return $allLogits;
    }

    /**
     * Backward pass — compute gradients for all parameters.
     * Uses BPTT (Backpropagation Through Time).
     *
     * @param array $allLogits  Logits from forward pass
     * @param int[] $targetIds  Target token IDs (next word at each position)
     * @return float Average loss for this sequence
     */
    public function backward(array $allLogits, array $targetIds): float {
        $seqLen = count($targetIds);
        $H = $this->hiddenSize;

        // Zero all gradients
        $this->embedding->zeroGrad();
        $this->lstm1->zeroGrad();
        $this->lstm2->zeroGrad();
        $this->outputLayer->zeroGrad();

        // Initialize backward LSTM state gradients
        $dh2Next = Tensor::vecZeros($H);
        $dc2Next = Tensor::vecZeros($H);
        $dh1Next = Tensor::vecZeros($H);
        $dc1Next = Tensor::vecZeros($H);

        $totalLoss = 0.0;

        // Backward through time (reverse order)
        for ($t = $seqLen - 1; $t >= 0; $t--) {
            // 1. Softmax + Cross-entropy gradient (combined for stability)
            $lossResult = LossFunctions::softmaxCrossEntropyGrad($allLogits[$t], $targetIds[$t]);
            $totalLoss += $lossResult['loss'];
            $dLogits = $lossResult['gradient'];

            // 2. Linear layer backward → gradient for h2
            $dh2FromOutput = $this->outputLayer->backward($dLogits, $t);

            // 3. LSTM2 backward
            $dh2Total = Tensor::vecAdd($dh2FromOutput, $dh2Next);
            [$dh2Prev, $dc2Prev, $dh1FromLstm2] = $this->lstm2->backward($dh2Total, $dc2Next, $t);
            $dh2Next = $dh2Prev;
            $dc2Next = $dc2Prev;

            // 4. LSTM1 backward
            $dh1Total = Tensor::vecAdd($dh1FromLstm2, $dh1Next);
            [$dh1Prev, $dc1Prev, $dEmb] = $this->lstm1->backward($dh1Total, $dc1Next, $t);
            $dh1Next = $dh1Prev;
            $dc1Next = $dc1Prev;

            // 5. Embedding backward
            $this->embedding->backward($dEmb, $this->lastTokenIds[$t]);
        }

        return $totalLoss / max($seqLen, 1);
    }

    /**
     * Apply optimizer updates to all parameters.
     */
    public function optimizerStep(AdamOptimizer $optimizer): void {
        $optimizer->beginStep();

        // Embedding
        $optimizer->updateTensor('emb_W', $this->embedding->weights, $this->embedding->gradients);

        // LSTM1
        $optimizer->updateTensor('lstm1_W', $this->lstm1->W, $this->lstm1->dW);
        $optimizer->updateArray('lstm1_b', $this->lstm1->b, $this->lstm1->db);

        // LSTM2
        $optimizer->updateTensor('lstm2_W', $this->lstm2->W, $this->lstm2->dW);
        $optimizer->updateArray('lstm2_b', $this->lstm2->b, $this->lstm2->db);

        // Output linear
        $optimizer->updateTensor('out_W', $this->outputLayer->weights, $this->outputLayer->dWeights);
        $optimizer->updateArray('out_b', $this->outputLayer->bias, $this->outputLayer->dBias);
    }

    /**
     * Clear all cached data (free memory after each training step).
     */
    public function clearCache(): void {
        $this->lstm1->clearCache();
        $this->lstm2->clearCache();
        $this->outputLayer->clearCache();
    }

    /**
     * Generate text autoregressively.
     *
     * @param int[]  $seedTokenIds   Initial token sequence (encoded prompt)
     * @param int    $maxLen         Maximum tokens to generate
     * @param float  $temperature    Sampling temperature (lower = more deterministic)
     * @param int    $topK           Top-K sampling (0 = sample from full distribution)
     * @param float  $repPenalty     Repetition penalty (>1 discourages repetition)
     * @return int[] Generated token IDs (excluding the seed)
     */
    public function generate(
        array $seedTokenIds,
        int $maxLen = 50,
        float $temperature = 0.8,
        int $topK = 40,
        float $repPenalty = 1.2
    ): array {
        $H = $this->hiddenSize;

        // Initialize LSTM states
        $h1 = Tensor::vecZeros($H);
        $c1 = Tensor::vecZeros($H);
        $h2 = Tensor::vecZeros($H);
        $c2 = Tensor::vecZeros($H);

        // Process seed (context)
        foreach ($seedTokenIds as $tokenId) {
            $emb = $this->embedding->forward($tokenId);
            [$h1, $c1] = $this->lstm1->step($emb, $h1, $c1, 0);
            [$h2, $c2] = $this->lstm2->step($h1, $h2, $c2, 0);
        }

        // Generate
        $generated = [];
        $lastTokenId = end($seedTokenIds) ?: Vocabulary::BOS_ID;
        $seenTokens = array_count_values($seedTokenIds);

        for ($step = 0; $step < $maxLen; $step++) {
            $emb = $this->embedding->forward($lastTokenId);
            [$h1, $c1] = $this->lstm1->step($emb, $h1, $c1, 0);
            [$h2, $c2] = $this->lstm2->step($h1, $h2, $c2, 0);
            $logits = $this->outputLayer->forward($h2, 0);

            // Apply repetition penalty
            if ($repPenalty > 1.0) {
                foreach ($seenTokens as $tokenId => $count) {
                    if (isset($logits[$tokenId])) {
                        $logits[$tokenId] /= ($repPenalty * $count);
                    }
                }
            }

            // Suppress special tokens during generation
            $logits[Vocabulary::PAD_ID] = -1e9;
            $logits[Vocabulary::UNK_ID] = -1e9;
            $logits[Vocabulary::BOS_ID] = -1e9;

            // Temperature scaling
            if ($temperature > 0 && $temperature != 1.0) {
                $logits = array_map(fn($x) => $x / $temperature, $logits);
            }

            // Sample next token
            $nextToken = $this->sampleTopK($logits, $topK);

            // Stop at EOS
            if ($nextToken === Vocabulary::EOS_ID) break;

            $generated[] = $nextToken;
            $lastTokenId = $nextToken;
            $seenTokens[$nextToken] = ($seenTokens[$nextToken] ?? 0) + 1;

            // Safety: stop if stuck in a loop
            if (count($generated) > 3) {
                $last3 = array_slice($generated, -3);
                if ($last3[0] === $last3[1] && $last3[1] === $last3[2]) break;
            }
        }

        return $generated;
    }

    /**
     * Top-K sampling from logits.
     * Selects from the K most likely tokens proportional to their probability.
     */
    private function sampleTopK(array $logits, int $k = 40): int {
        if ($k <= 0 || $k >= count($logits)) {
            // Full distribution sampling
            $probs = Tensor::softmax($logits);
            return $this->sampleFromProbs($probs);
        }

        // Find top-K indices
        arsort($logits);
        $topIndices = array_slice(array_keys($logits), 0, $k, true);

        // Extract top-K logits and compute softmax over them
        $topLogits = [];
        foreach ($topIndices as $idx) {
            $topLogits[$idx] = $logits[$idx];
        }

        $max = max($topLogits);
        $expSum = 0.0;
        $probs = [];
        foreach ($topLogits as $idx => $val) {
            $e = exp($val - $max);
            $probs[$idx] = $e;
            $expSum += $e;
        }

        // Normalize
        foreach ($probs as $idx => &$p) {
            $p /= max($expSum, 1e-15);
        }

        return $this->sampleFromProbs($probs);
    }

    /**
     * Sample a single index from a probability distribution.
     */
    private function sampleFromProbs(array $probs): int {
        $r = mt_rand(0, mt_getrandmax()) / mt_getrandmax();
        $cumulative = 0.0;
        foreach ($probs as $idx => $p) {
            $cumulative += $p;
            if ($r <= $cumulative) {
                return $idx;
            }
        }
        // Fallback: return the last index
        return array_key_last($probs) ?? 0;
    }

    /**
     * Get total parameter count.
     */
    public function paramCount(): int {
        return $this->embedding->paramCount()
             + $this->lstm1->paramCount()
             + $this->lstm2->paramCount()
             + $this->outputLayer->paramCount();
    }

    /**
     * Save model weights to disk.
     */
    public function save(string $path): void {
        ModelSerializer::save($path, [
            'config' => [
                'vocab_size' => $this->vocabSize,
                'embedding_dim' => $this->embeddingDim,
                'hidden_size' => $this->hiddenSize,
            ],
            'embedding' => $this->embedding->exportWeights(),
            'lstm1' => $this->lstm1->exportWeights(),
            'lstm2' => $this->lstm2->exportWeights(),
            'output' => $this->outputLayer->exportWeights(),
        ]);
    }

    /**
     * Load model weights from disk.
     */
    public function load(string $path): bool {
        $data = ModelSerializer::load($path);
        if (!$data) return false;

        if (isset($data['embedding'])) $this->embedding->importWeights($data['embedding']);
        if (isset($data['lstm1'])) $this->lstm1->importWeights($data['lstm1']);
        if (isset($data['lstm2'])) $this->lstm2->importWeights($data['lstm2']);
        if (isset($data['output'])) $this->outputLayer->importWeights($data['output']);

        return true;
    }

    /**
     * Get the default model file path.
     */
    public static function defaultModelPath(): string {
        return dirname(__DIR__, 2) . '/storage/models/lstm_lm.bin';
    }
}
