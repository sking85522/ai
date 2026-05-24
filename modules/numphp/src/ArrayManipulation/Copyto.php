<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class Copyto
{
    public static function copyto(NDArray &$dst, NDArray $src): void
    {
        $dst->setData($src->getData());
    }
}
