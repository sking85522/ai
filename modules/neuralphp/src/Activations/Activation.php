<?php

namespace NeuralPHP\Activations;

interface Activation
{
    /**
     * Apply activation function to a scalar value or array.
     */
    public function forward($z);

    /**
     * Compute the derivative of the activation function with respect to the input z.
     */
    public function derivative($z);
}
