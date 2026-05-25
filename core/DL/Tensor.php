<?php
namespace Core\DL;

/**
 * HRITIK AI - TENSOR ENGINE
 * High-performance matrix and vector operations for neural network computation.
 * Uses flat arrays in row-major order for maximum PHP speed.
 *
 * A Tensor represents a 2D matrix of shape (rows × cols).
 * Internal storage: $data[i * cols + j] = element at row i, col j.
 */
class Tensor {

    /** @var float[] Flat array storing matrix data in row-major order */
    public array $data;

    /** @var int Number of rows */
    public int $rows;

    /** @var int Number of columns */
    public int $cols;

    /**
     * @param int   $rows Number of rows
     * @param int   $cols Number of columns
     * @param float[] $data Optional initial data (flat array, row-major)
     */
    public function __construct(int $rows, int $cols, array $data = []) {
        $this->rows = $rows;
        $this->cols = $cols;
        $this->data = $data ?: array_fill(0, $rows * $cols, 0.0);
    }

    // ================================================================
    // Factory Methods
    // ================================================================

    /**
     * Create a zero-filled tensor.
     */
    public static function zeros(int $rows, int $cols): self {
        return new self($rows, $cols);
    }

    /**
     * Xavier/Glorot initialization — optimal for sigmoid/tanh networks.
     * Uses Box-Muller transform for Gaussian random numbers.
     */
    public static function xavierInit(int $rows, int $cols): self {
        $scale = sqrt(2.0 / ($rows + $cols));
        $data = [];
        $size = $rows * $cols;
        for ($i = 0; $i < $size; $i += 2) {
            // Box-Muller transform: generate 2 Gaussian samples at a time
            $u1 = max(1e-10, mt_rand(1, mt_getrandmax()) / mt_getrandmax());
            $u2 = mt_rand(1, mt_getrandmax()) / mt_getrandmax();
            $mag = sqrt(-2.0 * log($u1));
            $z0 = $mag * cos(2.0 * M_PI * $u2) * $scale;
            $z1 = $mag * sin(2.0 * M_PI * $u2) * $scale;
            $data[] = $z0;
            if ($i + 1 < $size) {
                $data[] = $z1;
            }
        }
        return new self($rows, $cols, $data);
    }

    /**
     * He initialization — optimal for ReLU networks.
     */
    public static function heInit(int $rows, int $cols): self {
        $scale = sqrt(2.0 / $cols);
        $data = [];
        $size = $rows * $cols;
        for ($i = 0; $i < $size; $i += 2) {
            $u1 = max(1e-10, mt_rand(1, mt_getrandmax()) / mt_getrandmax());
            $u2 = mt_rand(1, mt_getrandmax()) / mt_getrandmax();
            $mag = sqrt(-2.0 * log($u1));
            $data[] = $mag * cos(2.0 * M_PI * $u2) * $scale;
            if ($i + 1 < $size) {
                $data[] = $mag * sin(2.0 * M_PI * $u2) * $scale;
            }
        }
        return new self($rows, $cols, $data);
    }

    // ================================================================
    // Element Access
    // ================================================================

    public function get(int $row, int $col): float {
        return $this->data[$row * $this->cols + $col];
    }

    public function set(int $row, int $col, float $val): void {
        $this->data[$row * $this->cols + $col] = $val;
    }

    /**
     * Get an entire row as a vector.
     */
    public function getRow(int $row): array {
        $offset = $row * $this->cols;
        return array_slice($this->data, $offset, $this->cols);
    }

    /**
     * Set an entire row from a vector.
     */
    public function setRow(int $row, array $vec): void {
        $offset = $row * $this->cols;
        for ($j = 0; $j < $this->cols; $j++) {
            $this->data[$offset + $j] = $vec[$j];
        }
    }

    /**
     * Add a vector to a specific row: row[i] += vec
     */
    public function addToRow(int $row, array $vec): void {
        $offset = $row * $this->cols;
        for ($j = 0; $j < $this->cols; $j++) {
            $this->data[$offset + $j] += $vec[$j];
        }
    }

    // ================================================================
    // Core Matrix Operations
    // ================================================================

    /**
     * Matrix-vector multiply: result = this @ vec
     * this: (rows × cols), vec: (cols,) → result: (rows,)
     *
     * This is the most performance-critical operation in the neural network.
     * Optimized with local variable caching to avoid repeated property lookups.
     */
    public function matvec(array $vec): array {
        $data = $this->data;
        $rows = $this->rows;
        $cols = $this->cols;
        $result = [];

        for ($i = 0; $i < $rows; $i++) {
            $offset = $i * $cols;
            $sum = 0.0;
            for ($j = 0; $j < $cols; $j++) {
                $sum += $data[$offset + $j] * $vec[$j];
            }
            $result[$i] = $sum;
        }

        return $result;
    }

    /**
     * Transpose-matrix-vector multiply: result = this^T @ vec
     * this: (rows × cols), vec: (rows,) → result: (cols,)
     *
     * Computes the transpose product without actually transposing the matrix.
     * Used in backward passes to propagate gradients.
     */
    public function transposeMulVec(array $vec): array {
        $data = $this->data;
        $rows = $this->rows;
        $cols = $this->cols;
        $result = array_fill(0, $cols, 0.0);

        for ($i = 0; $i < $rows; $i++) {
            $offset = $i * $cols;
            $vi = $vec[$i];
            if (abs($vi) < 1e-12) continue; // Skip near-zero for speed
            for ($j = 0; $j < $cols; $j++) {
                $result[$j] += $data[$offset + $j] * $vi;
            }
        }

        return $result;
    }

    /**
     * Add outer product to this tensor: this += a ⊗ b
     * a: (rows,), b: (cols,)
     *
     * Used for gradient accumulation in backward passes.
     * Result: this[i][j] += a[i] * b[j]
     */
    public function addOuterProduct(array $a, array $b): void {
        $cols = $this->cols;
        foreach ($a as $i => $ai) {
            if (abs($ai) < 1e-12) continue; // Skip near-zero for speed
            $offset = $i * $cols;
            foreach ($b as $j => $bj) {
                $this->data[$offset + $j] += $ai * $bj;
            }
        }
    }

    // ================================================================
    // Tensor Arithmetic
    // ================================================================

    /**
     * Scale all elements: this *= scalar
     */
    public function scale(float $scalar): void {
        $size = count($this->data);
        for ($i = 0; $i < $size; $i++) {
            $this->data[$i] *= $scalar;
        }
    }

    /**
     * Add another tensor element-wise with optional scale: this += other * scale
     */
    public function addScaled(Tensor $other, float $scale = 1.0): void {
        $size = count($this->data);
        for ($i = 0; $i < $size; $i++) {
            $this->data[$i] += $other->data[$i] * $scale;
        }
    }

    /**
     * Reset all values to zero.
     */
    public function zero(): void {
        $this->data = array_fill(0, $this->rows * $this->cols, 0.0);
    }

    /**
     * Create a deep copy of this tensor.
     */
    public function copy(): self {
        return new self($this->rows, $this->cols, $this->data);
    }

    /**
     * Compute L2 norm of all elements.
     */
    public function norm(): float {
        $sum = 0.0;
        foreach ($this->data as $val) {
            $sum += $val * $val;
        }
        return sqrt($sum);
    }

    /**
     * Clip all gradient values to [-maxVal, maxVal] to prevent exploding gradients.
     */
    public function clip(float $maxVal): void {
        $size = count($this->data);
        for ($i = 0; $i < $size; $i++) {
            if ($this->data[$i] > $maxVal) {
                $this->data[$i] = $maxVal;
            } elseif ($this->data[$i] < -$maxVal) {
                $this->data[$i] = -$maxVal;
            }
        }
    }

    /**
     * Total number of elements (parameters).
     */
    public function size(): int {
        return $this->rows * $this->cols;
    }

    // ================================================================
    // Static Vector Operations
    // These operate on 1D arrays (hidden states, biases, etc.)
    // ================================================================

    /**
     * Element-wise vector addition: result[i] = a[i] + b[i]
     */
    public static function vecAdd(array $a, array $b): array {
        $result = [];
        foreach ($a as $i => $val) {
            $result[$i] = $val + $b[$i];
        }
        return $result;
    }

    /**
     * Element-wise vector multiplication (Hadamard product): result[i] = a[i] * b[i]
     */
    public static function vecMul(array $a, array $b): array {
        $result = [];
        foreach ($a as $i => $val) {
            $result[$i] = $val * $b[$i];
        }
        return $result;
    }

    /**
     * Element-wise vector subtraction: result[i] = a[i] - b[i]
     */
    public static function vecSub(array $a, array $b): array {
        $result = [];
        foreach ($a as $i => $val) {
            $result[$i] = $val - $b[$i];
        }
        return $result;
    }

    /**
     * Scalar-vector multiplication: result[i] = a[i] * scale
     */
    public static function vecScale(array $a, float $scale): array {
        return array_map(fn($v) => $v * $scale, $a);
    }

    /**
     * Create a zero vector of given size.
     */
    public static function vecZeros(int $size): array {
        return array_fill(0, $size, 0.0);
    }

    /**
     * Compute L2 norm of a vector.
     */
    public static function vecNorm(array $v): float {
        $sum = 0.0;
        foreach ($v as $val) {
            $sum += $val * $val;
        }
        return sqrt($sum);
    }

    /**
     * Clip vector elements to [-maxVal, maxVal].
     */
    public static function vecClip(array $v, float $maxVal): array {
        return array_map(fn($x) => max(-$maxVal, min($maxVal, $x)), $v);
    }

    // ================================================================
    // Activation Functions (Vector versions)
    // ================================================================

    /**
     * Sigmoid activation: σ(x) = 1 / (1 + exp(-x))
     * Clamped to prevent overflow.
     */
    public static function sigmoid(array $v): array {
        return array_map(function ($x) {
            $x = max(-500.0, min(500.0, $x));
            return 1.0 / (1.0 + exp(-$x));
        }, $v);
    }

    /**
     * Tanh activation applied to a vector.
     */
    public static function tanhVec(array $v): array {
        return array_map('tanh', $v);
    }

    /**
     * Softmax: converts logits to probability distribution.
     * Numerically stable (subtracts max before exp).
     *
     * @param array $v Vector of logits
     * @return array Probability distribution summing to 1.0
     */
    public static function softmax(array $v): array {
        if (empty($v)) return [];
        $max = max($v);
        $exp = array_map(fn($x) => exp($x - $max), $v);
        $sum = array_sum($exp);
        if ($sum < 1e-15) $sum = 1e-15;
        return array_map(fn($x) => $x / $sum, $exp);
    }

    /**
     * Log-softmax for numerical stability in loss computation.
     */
    public static function logSoftmax(array $v): array {
        if (empty($v)) return [];
        $max = max($v);
        $shifted = array_map(fn($x) => $x - $max, $v);
        $logSumExp = log(array_sum(array_map('exp', $shifted)));
        return array_map(fn($x) => $x - $logSumExp, $shifted);
    }

    /**
     * Concatenate two vectors into one.
     */
    public static function concat(array $a, array $b): array {
        return array_merge($a, $b);
    }
}
