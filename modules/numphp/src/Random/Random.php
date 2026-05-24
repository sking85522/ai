<?php

namespace NumPHP\Random;

class Random
{
    public static function random(): float
    {
        return (float) mt_rand() / mt_getrandmax();
    }
}
