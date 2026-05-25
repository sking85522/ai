<?php

namespace ModelIO;

use ModelIO\Core\Serializer;

class ModelIO
{
    /**
     * Save an MLPHP or NeuralPHP model to a file.
     *
     * @param object $model The trained model instance.
     * @param string $filepath Path to save the model.
     * @param string $format 'binary' (default) or 'json' (if supported by the model).
     * @return bool True on success.
     */
    public static function save(object $model, string $filepath, string $format = 'binary'): bool
    {
        return Serializer::save($model, $filepath, $format);
    }

    /**
     * Load an MLPHP or NeuralPHP model from a file.
     *
     * @param string $filepath Path to the saved model.
     * @param string $format 'binary' (default) or 'json'.
     * @return object The restored model instance.
     */
    public static function load(string $filepath, string $format = 'binary'): object
    {
        return Serializer::load($filepath, $format);
    }
}
