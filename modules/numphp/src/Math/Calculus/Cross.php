<?php

namespace NumPHP\Math\Calculus;

use NumPHP\Core\NDArray;

class Cross
{
    /**
     * cross
     *
     * @param mixed ...$args
     * @return mixed
     */
    public static function cross(...$args)
    {
        if (count($args) < 2) {
            throw new \InvalidArgumentException("cross expects at least 2 arguments");
        }
        $a = $args[0];
        $b = $args[1];

        $aData = $a instanceof NDArray ? $a->getData() : $a;
        $bData = $b instanceof NDArray ? $b->getData() : $b;

        if (!is_array($aData) || !is_array($bData) || count($aData) !== 3 || count($bData) !== 3) {
            throw new \InvalidArgumentException("cross product requires 3-dimensional vectors");
        }

        $result = [
            $aData[1] * $bData[2] - $aData[2] * $bData[1],
            $aData[2] * $bData[0] - $aData[0] * $bData[2],
            $aData[0] * $bData[1] - $aData[1] * $bData[0]
        ];

        return new NDArray($result);
    }
}
