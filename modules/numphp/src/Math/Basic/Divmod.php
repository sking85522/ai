<?php

namespace NumPHP\Math\Basic;

use NumPHP\Core\NDArray;
use NumPHP\Utils\Helpers;

class Divmod
{
    public static function divmod(NDArray $a, $b): array
    {
        $quot = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) {
            return floor($x / $y);
        });
        $rem = Helpers::mapBinary($a->getData(), ($b instanceof NDArray) ? $b->getData() : $b, function ($x, $y) {
            return $x - floor($x / $y) * $y;
        });
        return [new NDArray($quot), new NDArray($rem)];
    }
}
