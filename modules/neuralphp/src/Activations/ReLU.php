<?php

namespace NeuralPHP\Activations;

class ReLU implements Activation
{
    public function forward($z)
    {
        if (is_array($z)) {
            $res = [];
            foreach ($z as $val) {
                $res[] = max(0.0, $val);
            }
            return $res;
        }
        return max(0.0, $z);
    }

    public function derivative($z)
    {
        if (is_array($z)) {
            $res = [];
            foreach ($z as $val) {
                $res[] = ($val > 0) ? 1.0 : 0.0;
            }
            return $res;
        }
        return ($z > 0) ? 1.0 : 0.0;
    }
}
