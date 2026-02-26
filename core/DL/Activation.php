<?php
class Activation
{
    public static function sigmoid(float $x): float
    {
        return 1.0 / (1.0 + exp(-$x));
    }

    public static function relu(float $x): float
    {
        return max(0.0, $x);
    }
}
