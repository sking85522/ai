<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class BroadcastTo
{
    public static function broadcast_to(NDArray $a, array $shape): NDArray
    {
        if ($a->getShape() === $shape) {
            return $a;
        }
        if (empty($a->getShape())) {
            $data = Helpers::buildFilled($shape, $a->getData());
            return new NDArray($data);
        }
        throw new \Exception('broadcast_to not implemented for this shape');
    }
}
