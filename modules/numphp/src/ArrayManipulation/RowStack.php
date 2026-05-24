<?php

namespace NumPHP\ArrayManipulation;

use NumPHP\Core\NDArray;

class RowStack
{
    public static function row_stack(array $tup): NDArray
    {
        return Vstack::vstack($tup);
    }
}
