<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Cov
{
    public static function cov(NDArray $m): NDArray
    {
        return Covariance::cov($m);
    }
}
