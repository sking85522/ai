<?php
class MatrixOps
{
    public function trySolve(string $input): ?string
    {
        $x = mb_strtolower(trim($input), 'UTF-8');
        if (preg_match('/det(?:erminant)?\s+of\s+\[\s*(-?\d+)\s*,\s*(-?\d+)\s*;\s*(-?\d+)\s*,\s*(-?\d+)\s*\]/u', $x, $m) === 1) {
            $a = (int) $m[1];
            $b = (int) $m[2];
            $c = (int) $m[3];
            $d = (int) $m[4];
            $det = ($a * $d) - ($b * $c);
            return 'Determinant = ' . $det;
        }
        return null;
    }
}
