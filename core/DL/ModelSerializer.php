<?php
namespace Core\DL;

/**
 * HRITIK AI - MODEL SERIALIZER
 * Saves and loads neural network weights to/from disk.
 * Uses PHP's native serialization for reliability.
 * Supports checkpointing during training for crash recovery.
 */
class ModelSerializer {

    /**
     * Save model data to a file.
     *
     * @param string $path    Absolute file path
     * @param array  $modelData Associative array of model parameters
     */
    public static function save(string $path, array $modelData): void {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Add metadata
        $modelData['__meta'] = [
            'saved_at' => date('c'),
            'php_version' => PHP_VERSION,
            'engine' => 'hritik_ai_neural',
        ];

        $serialized = serialize($modelData);
        // Write to temp file first, then rename for atomicity
        $tmp = $path . '.tmp';
        file_put_contents($tmp, $serialized);
        rename($tmp, $path);
    }

    /**
     * Load model data from a file.
     *
     * @param string $path Absolute file path
     * @return array|null Model data or null if not found
     */
    public static function load(string $path): ?array {
        if (!file_exists($path)) {
            return null;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }

        $data = @unserialize($raw);
        return is_array($data) ? $data : null;
    }

    /**
     * Check if a saved model exists.
     */
    public static function exists(string $path): bool {
        return file_exists($path) && filesize($path) > 0;
    }

    /**
     * Save a training checkpoint (model + optimizer state).
     * Keeps only the last 2 checkpoints to save disk space.
     *
     * @param string $dir           Directory for checkpoints
     * @param int    $epoch         Current epoch number
     * @param array  $modelData     Model weights
     * @param array  $optimizerState Optimizer moments (m, v, t)
     * @param array  $trainInfo     Training metadata (loss, accuracy, etc.)
     */
    public static function saveCheckpoint(
        string $dir,
        int $epoch,
        array $modelData,
        array $optimizerState = [],
        array $trainInfo = []
    ): void {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $modelData['__train_info'] = $trainInfo;
        self::save("$dir/checkpoint_epoch_{$epoch}.bin", $modelData);

        if (!empty($optimizerState)) {
            self::save("$dir/optimizer_epoch_{$epoch}.bin", $optimizerState);
        }

        // Clean old checkpoints (keep last 2)
        self::cleanOldCheckpoints($dir, $epoch);
    }

    /**
     * Load the latest checkpoint from a directory.
     *
     * @param string $dir Checkpoint directory
     * @return array|null ['epoch' => int, 'model' => array, 'optimizer' => array|null]
     */
    public static function loadLatestCheckpoint(string $dir): ?array {
        if (!is_dir($dir)) {
            return null;
        }

        // Find the highest epoch checkpoint
        $files = glob("$dir/checkpoint_epoch_*.bin");
        if (empty($files)) {
            return null;
        }

        $latestEpoch = -1;
        foreach ($files as $file) {
            if (preg_match('/checkpoint_epoch_(\d+)\.bin$/', $file, $m)) {
                $epoch = (int)$m[1];
                if ($epoch > $latestEpoch) {
                    $latestEpoch = $epoch;
                }
            }
        }

        if ($latestEpoch < 0) {
            return null;
        }

        $model = self::load("$dir/checkpoint_epoch_{$latestEpoch}.bin");
        if (!$model) {
            return null;
        }

        $optimizer = self::load("$dir/optimizer_epoch_{$latestEpoch}.bin");

        return [
            'epoch' => $latestEpoch,
            'model' => $model,
            'optimizer' => $optimizer,
        ];
    }

    /**
     * Remove old checkpoints, keeping only the last N.
     */
    private static function cleanOldCheckpoints(string $dir, int $currentEpoch, int $keep = 2): void {
        for ($e = $currentEpoch - $keep - 1; $e >= 0; $e--) {
            $modelFile = "$dir/checkpoint_epoch_{$e}.bin";
            $optFile = "$dir/optimizer_epoch_{$e}.bin";
            if (file_exists($modelFile)) @unlink($modelFile);
            if (file_exists($optFile)) @unlink($optFile);
        }
    }

    /**
     * Get the default models directory.
     */
    public static function modelsDir(): string {
        $dir = dirname(__DIR__, 2) . '/storage/models';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }
}
