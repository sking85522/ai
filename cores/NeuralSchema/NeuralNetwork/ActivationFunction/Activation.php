<?php
namespace Core\NeuralSchema\NeuralNetwork\ActivationFunction;

class Activation {
    public static function relu(float $x): float {
        return max(0, $x);
    }

    public static function sigmoid(float $x): float {
        return 1 / (1 + exp(-$x));
    }

    public static function tanh(float $x): float {
        return tanh($x);
    }

    public static function apply(float $x, string $type): float {
        return match ($type) {
            'relu' => self::relu($x),
            'sigmoid' => self::sigmoid($x),
            'tanh' => self::tanh($x),
            default => $x
        };
    }
}
