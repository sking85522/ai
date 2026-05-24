<?php

namespace NumPHP\Creation;

class Mgrid
{
    public static function mgrid(array $x, array $y): array
    {
        return Meshgrid::meshgrid($x, $y);
    }
}
