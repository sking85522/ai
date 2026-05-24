<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class BroadcastArrays
{
    public static function broadcast_arrays(NDArray $a, NDArray $b): array
    {
        $shapeA = $a->getShape();
        $shapeB = $b->getShape();
        if ($shapeA === $shapeB) {
            return [$a, $b];
        }
        if (empty($shapeA)) {
            $data = Helpers::buildFilled($shapeB, $a->getData());
            return [new NDArray($data), $b];
        }
        if (empty($shapeB)) {
            $data = Helpers::buildFilled($shapeA, $b->getData());
            return [$a, new NDArray($data)];
        }
        throw new \Exception('broadcast_arrays not implemented for these shapes');
    }
}
