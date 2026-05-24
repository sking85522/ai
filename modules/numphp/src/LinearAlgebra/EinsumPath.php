<?php

namespace NumPHP\LinearAlgebra;

class EinsumPath
{
    public static function einsum_path(string $subscripts): array
    {
        return [$subscripts, 'simple'];
    }
}
