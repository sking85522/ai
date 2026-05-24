<?php

namespace NeuralPHP\Activations;

class Sigmoid implements Activation
{
    public function forward($z)
    {
        if (is_array($z)) {
            $res = [];
            foreach ($z as $val) {
                $res[] = 1.0 / (1.0 + exp(-$val));
            }
            return $res;
        }
        return 1.0 / (1.0 + exp(-$z));
    }

    public function derivative($z)
    {
        if (is_array($z)) {
            $res = [];
            foreach ($z as $val) {
                $sig = 1.0 / (1.0 + exp(-$val));
                $res[] = $sig * (1 - $sig);
            }
            return $res;
        }
        $sig = 1.0 / (1.0 + exp(-$z));
        return $sig * (1 - $sig);
    }
}
