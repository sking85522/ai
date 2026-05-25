<?php

namespace ModelIO\Core;

class Serializer
{
    /**
     * Serialize a PHP object to a file.
     * Uses PHP's native serialize for accurate recreation of private states (Weights, Biases).
     */
    public static function save(object $model, string $filepath, string $format = 'binary'): bool
    {
        if ($format === 'binary') {
            $serialized = serialize($model);
            if (file_put_contents($filepath, $serialized) === false) {
                throw new \Exception("Failed to save model to $filepath");
            }
            return true;
        } elseif ($format === 'json') {
            // NOTE: json_encode won't capture private properties easily without JsonSerializable interface on the model.
            // But this serves as a placeholder if models implement it later.
            $json = json_encode($model, JSON_PRETTY_PRINT);
            if (file_put_contents($filepath, $json) === false) {
                throw new \Exception("Failed to save model to $filepath");
            }
            return true;
        }

        throw new \Exception("Unsupported format: $format");
    }

    /**
     * Unserialize a PHP object from a file.
     */
    public static function load(string $filepath, string $format = 'binary'): object
    {
        if (!file_exists($filepath)) {
            throw new \Exception("Model file not found: $filepath");
        }

        $content = file_get_contents($filepath);
        if ($content === false) {
            throw new \Exception("Failed to read model file: $filepath");
        }

        if ($format === 'binary') {
            $model = unserialize($content);
            if ($model === false && $content !== 'b:0;') {
                throw new \Exception("Failed to unserialize model from $filepath");
            }
            return $model;
        } elseif ($format === 'json') {
            $model = json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Failed to decode JSON model: " . json_last_error_msg());
            }
            return $model;
        }

        throw new \Exception("Unsupported format: $format");
    }
}
