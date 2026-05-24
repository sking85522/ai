<?php

class torch {
    public static function tensor($data, $requires_grad = false): \PHPTorch\Tensor {
        return new \PHPTorch\Tensor($data, [], '', $requires_grad);
    }

    public static function zeros(int ...$shape): \PHPTorch\Tensor {
        $data = self::buildZeros($shape);
        return new \PHPTorch\Tensor($data);
    }

    private static function buildZeros(array $shape) {
        if (count($shape) === 1) {
            return array_fill(0, $shape[0], 0.0);
        }
        $dim = array_shift($shape);
        $res = [];
        for ($i = 0; $i < $dim; $i++) {
            $res[] = self::buildZeros($shape);
        }
        return $res;
    }
}
