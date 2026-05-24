<?php

namespace NumPHP\Statistics;

use NumPHP\Core\NDArray;

class Corrcoef
{
    /**
     * Return Pearson product-moment correlation coefficients.
     *
     * @param NDArray $x
     * @return NDArray
     */
    public static function corrcoef(NDArray $x): NDArray
    {
        $c = Covariance::cov($x);
        $data = $c->getData();
        $shape = $c->getShape();

        if (empty($shape)) return new NDArray(1.0); // Scalar case

        $d = [];
        // Diagonal elements are variances
        for ($i = 0; $i < $shape[0]; $i++) {
            $d[$i] = sqrt($data[$i][$i]);
        }

        $corr = [];
        for ($i = 0; $i < $shape[0]; $i++) {
            for ($j = 0; $j < $shape[1]; $j++) {
                $corr[$i][$j] = $data[$i][$j] / ($d[$i] * $d[$j]);
            }
        }

        return new NDArray($corr);
    }
}