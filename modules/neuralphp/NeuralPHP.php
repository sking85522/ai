<?php

namespace NeuralPHP;

use NeuralPHP\Models\Sequential;
use NeuralPHP\Layers\Dense;
use NeuralPHP\Layers\Dropout;
use NeuralPHP\Layers\BatchNorm;
use NeuralPHP\Layers\Embedding;
use NeuralPHP\Layers\Reasoning;
use NeuralPHP\Layers\Flatten;
use NeuralPHP\Tokenizer\TextTokenizer;
use NeuralPHP\Activations\Sigmoid;
use NeuralPHP\Activations\ReLU;
use NeuralPHP\Activations\Tanh;
use NeuralPHP\Activations\Softmax;
use NeuralPHP\Activations\LeakyReLU;
use NeuralPHP\Losses\MSE;
use NeuralPHP\Losses\CrossEntropy;
use NeuralPHP\Losses\BinaryCrossEntropy;
use NeuralPHP\Optimizers\SGD;
use NeuralPHP\Optimizers\Adam;
use NeuralPHP\Optimizers\RMSprop;

class NeuralPHP
{
    // ──────────── Models ────────────

    public static function Sequential(): Sequential
    {
        return new Sequential();
    }

    // ──────────── Layers ────────────

    public static function Dense(int $input_size, int $output_size, $activation = null): Dense
    {
        return new Dense($input_size, $output_size, $activation);
    }

    public static function Dropout(float $rate = 0.5): Dropout
    {
        return new Dropout($rate);
    }

    public static function BatchNorm(int $size, float $epsilon = 1e-5): BatchNorm
    {
        return new BatchNorm($size, $epsilon);
    }

    public static function Embedding(int $vocab_size, int $embedding_dim): Embedding
    {
        return new Embedding($vocab_size, $embedding_dim);
    }

    public static function Reasoning(int $dim): Reasoning
    {
        return new Reasoning($dim);
    }

    public static function Flatten(): Flatten
    {
        return new Flatten();
    }

    // ──────────── Tokenizer ────────────

    public static function TextTokenizer(): TextTokenizer
    {
        return new TextTokenizer();
    }

    // ──────────── Activations ────────────

    public static function getActivation(string $name)
    {
        switch (strtolower($name)) {
            case 'sigmoid': return new Sigmoid();
            case 'relu': return new ReLU();
            case 'tanh': return new Tanh();
            case 'softmax': return new Softmax();
            case 'leaky_relu':
            case 'leakyrelu': return new LeakyReLU();
            default: throw new \Exception("Activation '$name' not found.");
        }
    }

    // ──────────── Losses ────────────

    public static function getLoss(string $name)
    {
        switch (strtolower($name)) {
            case 'mse': return new MSE();
            case 'crossentropy':
            case 'cross_entropy':
            case 'categorical_crossentropy': return new CrossEntropy();
            case 'binary_crossentropy':
            case 'bce': return new BinaryCrossEntropy();
            default: throw new \Exception("Loss '$name' not found.");
        }
    }

    // ──────────── Optimizers ────────────

    public static function getOptimizer(string $name, float $lr = 0.01)
    {
        switch (strtolower($name)) {
            case 'sgd': return new SGD($lr);
            case 'adam': return new Adam($lr);
            case 'rmsprop': return new RMSprop($lr);
            default: throw new \Exception("Optimizer '$name' not found.");
        }
    }
}
